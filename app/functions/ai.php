<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

class AIService
{
    private static function log($message)
    {
        $logFile = __DIR__ . '/../../ai_error.log';
        $date = date('Y-m-d H:i:s');
        error_log("[$date] $message\n", 3, $logFile);
    }

    /**
     * Generate AI Summary for a post
     * 
     * @param int $cid Post ID
     * @return array ['success' => bool, 'message' => string, 'summary' => string]
     */
    public static function generateSummary($cid)
    {
        // 1. Get Config
        $endpoint = Get::Options('ai_api_endpoint', false);
        $apiKey = Get::Options('ai_api_key', false);
        $model = Get::Options('ai_model', false);
        $promptTemplate = Get::Options('ai_prompt_template', false);

        // Default values if not set (fallback)
        if (!$endpoint) $endpoint = 'https://api.openai.com/v1/chat/completions';
        if (!$model) $model = 'gpt-3.5-turbo';
        if (!$promptTemplate) $promptTemplate = "请为以下文章生成一个简短的摘要（200字以内）：\n\n标题：\${title}\n\n内容：\n\${content}";

        if (!$endpoint || !$apiKey) {
            self::log('Missing API Configuration');
            return ['success' => false, 'message' => 'Missing API Configuration'];
        }

        // 2. Get Post Content
        $db = Typecho_Db::get();
        $post = $db->fetchRow($db->select()->from('table.contents')->where('cid = ?', $cid));
        if (!$post) {
            self::log('Post not found: ' . $cid);
            return ['success' => false, 'message' => 'Post not found: ' . $cid];
        }

        // 3. Prepare Prompt
        $title = $post['title'];
        $content = strip_tags($post['text']); // Simple strip tags

        // Remove markdown characters to save tokens (simplified)
        $content = preg_replace('/[#*`~>\[\]\(\)]/', ' ', $content);
        $content = preg_replace('/\s+/', ' ', $content); // Collapse whitespace

        // Truncate content to avoid token limits (approx 3000 chars)
        if (mb_strlen($content) > 3000) {
            $content = mb_substr($content, 0, 3000) . '...';
        }

        $prompt = str_replace(['${title}', '${content}'], [$title, $content], $promptTemplate);

        // 4. Call OpenAI
        $data = [
            'model' => $model,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.7
        ];

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 30s timeout

        // Disable SSL verification for local development environments
        // This addresses the "SSL certificate problem: unable to get local issuer certificate" error
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            self::log('Curl Error: ' . $curlError);
            return ['success' => false, 'message' => 'Curl Error: ' . $curlError];
        }

        if ($httpCode !== 200) {
            self::log('API Error: ' . $httpCode . ' Response: ' . $response);
            return ['success' => false, 'message' => 'API Error: ' . $httpCode . ' Response: ' . $response];
        }

        $result = json_decode($response, true);
        $summary = $result['choices'][0]['message']['content'] ?? '';

        if (!$summary) {
            self::log('Empty response from AI or invalid JSON. Response: ' . $response);
            return ['success' => false, 'message' => 'Empty response from AI or invalid JSON'];
        }

        $summary = trim($summary);

        // 5. Save to Database (Custom Field)
        // Check if field exists in table.fields
        $field = $db->fetchRow($db->select()->from('table.fields')
            ->where('cid = ?', $cid)
            ->where('name = ?', 'AISummary'));

        if ($field) {
            $db->query($db->update('table.fields')
                ->rows(['str_value' => $summary])
                ->where('cid = ?', $cid)
                ->where('name = ?', 'AISummary'));
        } else {
            $db->query($db->insert('table.fields')
                ->rows([
                    'cid' => $cid,
                    'name' => 'AISummary',
                    'type' => 'str',
                    'str_value' => $summary,
                    'int_value' => 0,
                    'float_value' => 0
                ]));
        }

        return ['success' => true, 'summary' => $summary];
    }

    /**
     * Generate AI Summary synchronously when publishing a post
     * Hook for Widget_Contents_Post_Edit::finishPublish
     * 
     * @param array $contents Post contents
     * @param Typecho_Widget $edit Edit widget
     */
    public static function generateOnPublish($contents, $edit)
    {
        // Check if auto-generate is enabled
        $auto = Get::Options('ai_auto_generate', false);
        if ($auto !== 'on') return;

        $cid = $contents['cid'];

        // Direct synchronous call
        // Note: This might block the user interface for a few seconds
        self::generateSummary($cid);
    }
}
