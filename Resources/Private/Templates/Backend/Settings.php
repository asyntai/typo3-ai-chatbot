<?php /* Variables available: $siteId, $accountEmail, $scriptUrl */ ?>
<style>
#asyntai-settings-wrap {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    padding: 24px;
    line-height: 1.5;
}
#asyntai-settings-wrap h1 {
    font-size: 20px;
    font-weight: 600;
    margin: 0 0 24px 0;
    color: #333;
}
#asyntai-settings-wrap h2 {
    font-size: 18px;
    font-weight: 600;
    margin: 0 0 12px 0;
    color: #333;
}
#asyntai-settings-wrap p {
    margin: 0 0 20px 0;
    font-size: 14px;
    color: #333;
}
#asyntai-settings-wrap button, #asyntai-settings-wrap a.btn {
    font-size: 14px;
    padding: 8px 20px;
    border-radius: 4px;
    font-weight: 500;
    cursor: pointer;
    border: none;
    text-decoration: none;
    display: inline-block;
}
#asyntai-settings-wrap .btn-primary {
    background: #2563eb;
    color: #fff;
}
#asyntai-settings-wrap .btn-primary:hover {
    background: #1e5bd8;
}
#asyntai-settings-wrap .btn-default {
    background: #fff;
    color: #333;
    border: 1px solid #ddd;
}
#asyntai-settings-wrap .btn-default:hover {
    background: #f5f5f5;
}
#asyntai-settings-wrap #asyntai-alert {
    padding: 12px 16px;
    border-radius: 4px;
    margin: 0 0 20px 0;
    font-size: 14px;
}
</style>
<div id="asyntai-settings-wrap">
    <div style="max-width:960px;margin:0 auto;">
        <h1>Asyntai AI Chatbot</h1>

        <p id="asyntai-status">
            Status: <span style="color:<?= $siteId ? '#28a745' : '#dc3545' ?>;font-weight:600;"><?= $siteId ? 'Connected' : 'Not connected' ?></span><?php if ($siteId && $accountEmail): ?> as <?= $accountEmail ?><?php endif; ?>
            <?php if ($siteId): ?>
                <button id="asyntai-reset" class="btn btn-default" style="margin-left:12px;">Reset</button>
            <?php endif; ?>
        </p>

        <div id="asyntai-alert" style="display:none;"></div>

        <div id="asyntai-connected-box" style="display:<?= $siteId ? 'block' : 'none' ?>;">
            <div style="padding:32px;border:1px solid #ddd;border-radius:8px;background:#fff;text-align:center;">
                <h2>Asyntai is now enabled</h2>
                <p style="font-size:16px;color:#666;">Set up your AI chatbot, review chat logs and more:</p>
                <a class="btn btn-primary" href="https://asyntai.com/dashboard" target="_blank" rel="noopener">Open Asyntai Panel</a>
                <p style="margin:20px 0 0;color:#666;">
                    <strong>Tip:</strong> If you want to change how the AI answers, please <a href="https://asyntai.com/dashboard#setup" target="_blank" rel="noopener" style="color:#2563eb;text-decoration:underline;">go here</a>.
                </p>
            </div>
        </div>

        <div id="asyntai-popup-wrap" style="display:<?= $siteId ? 'none' : 'block' ?>;">
            <div style="padding:32px;border:1px solid #ddd;border-radius:8px;background:#fff;text-align:center;">
                <p style="font-size:16px;color:#333;">Create a free Asyntai account or sign in to enable the chatbot</p>
                <button id="asyntai-connect-btn" class="btn btn-primary">Get started</button>
                <p style="margin:16px 0 0;color:#666;">If it doesn't work, <a href="#" id="asyntai-fallback-link" target="_blank" rel="noopener" style="color:#2563eb;text-decoration:underline;">open the connect window</a>.</p>
            </div>
        </div>
    </div>
</div>


