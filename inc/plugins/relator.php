<?php

/**
 * Relator
 *
 * Describe the qualification of your outgoing links.
 * Adds `nofollow ugc noopener external` to external links.
 *
 * @author Medet "tedem" Erdal <hello@tedem.dev>
 */

// mybb
if (! \defined('IN_MYBB')) {
    die('(-_*) This file cannot be accessed directly.');
}

// version controls
if (version_compare(phpversion(), '7.0', '<=') || version_compare(phpversion(), '8.3', '>=')) {
    die('(T_T) PHP version is not compatible for this plugin!');
}

// constants
\define('TEDEM_RELATOR_ID', 'relator');
\define('TEDEM_RELATOR_NAME', ucfirst(TEDEM_RELATOR_ID));
\define('TEDEM_RELATOR_AUTHOR', 'tedem');
\define('TEDEM_RELATOR_VERSION', '1.0.0');

// hooks
if (! \defined('IN_ADMINCP')) {
    $plugins->add_hook('postbit', 'relator_main');
}

function relator_info(): array
{
    $description = <<<'HTML'
<div style="margin-top: 1em;">
    Another nofollow plugin. By adding <b>rel="nofollow ugc noopener external"</b> tag to your outbound links, it informs the bots about the quality of your link and ensures that you don't give backlinks to third-party sites.
</div>
HTML;

    if (relator_donation_status()) {
        $description = $description . relator_donation();
    }

    return [
        'name'          => TEDEM_RELATOR_NAME,
        'description'   => $description,
        'website'       => 'https://tedem.dev',
        'author'        => TEDEM_RELATOR_AUTHOR,
        'authorsite'    => 'https://tedem.dev',
        'version'       => TEDEM_RELATOR_VERSION,
        'codename'      => TEDEM_RELATOR_AUTHOR . '_' . TEDEM_RELATOR_ID,
        'compatibility' => '18*',
    ];
}

function relator_install(): void
{
    global $cache;

    // add cache
    $plugins = $cache->read(TEDEM_RELATOR_AUTHOR);

    $plugins[TEDEM_RELATOR_ID] = [
        'name'     => TEDEM_RELATOR_NAME,
        'author'   => TEDEM_RELATOR_AUTHOR,
        'version'  => TEDEM_RELATOR_VERSION,
        'donation' => 1,
    ];

    $cache->update(TEDEM_RELATOR_AUTHOR, $plugins);
}

function relator_is_installed(): bool
{
    global $cache;

    // has cache
    $plugins = $cache->read(TEDEM_RELATOR_AUTHOR);

    if (isset($plugins[TEDEM_RELATOR_ID])) {
        return true;
    }

    return false;
}

function relator_uninstall(): void
{
    global $db, $cache;

    // remove cache
    $plugins = $cache->read(TEDEM_RELATOR_AUTHOR);

    unset($plugins[TEDEM_RELATOR_ID]);

    $cache->update(TEDEM_RELATOR_AUTHOR, $plugins);

    if (\count($plugins) == 0) {
        $db->delete_query('datacache', "title='" . TEDEM_RELATOR_AUTHOR . "'");
    }
}

function relator_activate(): void
{
    //
}

function relator_deactivate(): void
{
    //
}

function relator_main($post): array
{
    global $mybb;

    $contents = $post['message'];

    // Is everything okay?
    if (empty($contents) || ! class_exists('DOMDocument') || ! class_exists('DOMXPath')) {
        return $post;
    }

    $url = $mybb->settings['bburl'] ?? '';

    $contents = mb_convert_encoding($contents, 'HTML-ENTITIES', 'UTF-8');

    $dom = new \DOMDocument();
    $dom->loadHTML('<div>' . $contents . '</div>', \LIBXML_HTML_NOIMPLIED | \LIBXML_HTML_NODEFDTD);

    $xpath = new \DOMXPath($dom);
    $links = $xpath->query("//a[not(starts-with(@href, '" . $url . "'))]");

    $rel_attribute = 'rel';
    $rel_value     = 'nofollow ugc noopener external';

    foreach ($links as $link) {
        $link->setAttribute($rel_attribute, $rel_value);
    }

    // Remove HTML `<div>` tag.
    $contents = mb_substr($dom->saveHTML(), 5, -7, 'UTF-8');

    $post['message'] = $contents;

    return $post;
}

function relator_donation(): string
{
    global $mybb;

    relator_donation_edit();

    $BMC  = '<a href="https://www.buymeacoffee.com/tedem"><b>Buy me a coffee</b></a>';
    $KOFI = '<a href="https://ko-fi.com/tedem"><b>KO-FI</b></a>';

    $close_link   = 'index.php?module=config-plugins&' . TEDEM_RELATOR_ID . '=deactivate-donation&my_post_key=' . $mybb->post_code;
    $close_button = ' &mdash; <a href="' . $close_link . '"><b>Close Donation</b></a>';

    $message = '<b>Donation:</b> Support for new plugins, themes, etc. via ' . $BMC . ' or ' . $KOFI . $close_button;

    return '<div style="margin-top: 1em;">' . $message . '</div>';
}

function relator_donation_status(): bool
{
    global $cache;

    $donation = $cache->read(TEDEM_RELATOR_AUTHOR);

    if (isset($donation[TEDEM_RELATOR_ID]['donation']) && $donation[TEDEM_RELATOR_ID]['donation'] == 1) {
        return true;
    }

    return false;
}

function relator_donation_edit(): void
{
    global $mybb;

    if ($mybb->get_input('my_post_key') == $mybb->post_code) {
        global $cache;

        $plugins = $cache->read(TEDEM_RELATOR_AUTHOR);

        if ($mybb->get_input('relator') == 'deactivate-donation') {
            $plugins[TEDEM_RELATOR_ID]['donation'] = 0;

            $cache->update(TEDEM_RELATOR_AUTHOR, $plugins);

            flash_message('The donation message has been successfully closed.', 'success');
            admin_redirect('index.php?module=config-plugins');
        }
    }
}
