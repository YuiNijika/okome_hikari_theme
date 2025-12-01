<?php

declare(strict_types=1);

if (!defined('__TYPECHO_ROOT_DIR__')) exit;

// AI Controller
class AIController extends BaseController
{
    public function handleGenerate(): array
    {
        $cid = (int)($this->request->getQuery('cid', 0));
        // Try to get CID from POST if not in GET
        if ($cid === 0 && isset($_POST['cid'])) {
            $cid = (int)$_POST['cid'];
        }

        $trigger = $this->request->getQuery('trigger', '');

        if ($cid <= 0) {
            $this->response->error('Invalid CID', HttpCode::BAD_REQUEST);
        }

        // Check permissions
        if ($trigger === 'async') {
            // Verify Signature for internal async calls
            $time = (int)$this->request->getQuery('time', 0);
            $sign = $this->request->getQuery('sign', '');

            // 5 minutes tolerance
            if (abs(time() - $time) > 300) {
                $this->response->error('Request expired', HttpCode::FORBIDDEN);
            }

            $key = md5(Typecho_Widget::widget('Widget_Options')->secret);
            $expectedSign = md5($cid . $time . $key);

            if ($sign !== $expectedSign) {
                $this->response->error('Invalid signature', HttpCode::FORBIDDEN);
            }
        } else {
            // Manual call - Check User Login
            $user = Typecho_Widget::widget('Widget_User');
            if (!$user->hasLogin() || !$user->pass('editor', true)) {
                $this->response->error('Unauthorized', HttpCode::UNAUTHORIZED);
            }

            // CSRF Protection
            $token = $_POST['token'] ?? $_GET['token'] ?? '';
            $security = Typecho_Widget::widget('Widget_Security');
            if ($token !== $security->getToken('ai-summary-generate')) {
                $this->response->error('Invalid Security Token', HttpCode::FORBIDDEN);
            }
        }

        // Ensure Service is loaded
        if (!class_exists('AIService')) {
            $aiServicePath = __DIR__ . '/../../../app/functions/ai.php';
            if (file_exists($aiServicePath)) {
                require_once $aiServicePath;
            } else {
                $this->response->error('AI Service file not found', HttpCode::INTERNAL_ERROR);
            }
        }

        $result = AIService::generateSummary($cid);

        if (!$result['success']) {
            $this->response->error($result['message'], HttpCode::INTERNAL_ERROR);
        }

        return ['data' => ['summary' => $result['summary']]];
    }
}
