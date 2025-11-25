<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$prevPost = null;
try {
    $archive = GetPost::getCurrentArchive();
    $db = Typecho_Db::get();
    $row = $db->fetchRow(
        $db->select()
            ->from('table.contents')
            ->where('status = ? AND type = ? AND created < ?', 'publish', 'post', $archive->created)
            ->order('created', Typecho_Db::SORT_DESC)
            ->limit(1)
    );
    if ($row) {
        $prevPost = [
            'cid' => $row['cid'],
            'title' => $row['title'],
            'permalink' => Helper::widgetById('Contents', $row['cid'])->permalink,
        ];
    }
} catch (Exception $e) {
}
?>
<?php if (!empty($prevPost)): ?>
    <a href="<?php echo htmlspecialchars($prevPost['permalink'], ENT_QUOTES, 'UTF-8'); ?>" class="block mt-6">
        <div class="mx-auto w-full max-w-full sm:max-w-5xl sm:px-4 flex flex-col gap-6  hover:scale-101 transition-transform duration-300">
            <div class="card-body card bg-base-100 p-4 md:p-8 shadow-sm">
                <div class="card-title">上一篇</div>
                <p class="text-lg font-semibold"><?php echo htmlspecialchars($prevPost['title'], ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
        </div>
    </a>
<?php endif; ?>