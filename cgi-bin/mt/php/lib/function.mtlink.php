<?php
# Movable Type (r) (C) 2001-2010 Six Apart, Ltd. All Rights Reserved.
# This code cannot be redistributed without permission from www.sixapart.com.
# For more information, consult your Movable Type license.
#
# $Id: function.mtlink.php 3455 2009-02-23 02:29:31Z auno $

function smarty_function_mtlink($args, &$ctx) {
    // status: incomplete
    // parameters: template, entry_id
    static $_template_links = array();
    if (isset($args['template'])) {
        $name = $args['template'];
        $cache_key = $ctx->__stash['blog_id'] . ';' . $name;
        if (isset($_template_links[$cache_key])) {
            $link = $_template_links[$cache_key];
        } else {
            $tmpl = $ctx->mt->db->load_index_template($ctx, $name);
            $blog = $ctx->stash('blog');
            $site_url = $blog['blog_site_url'];
            if (!preg_match('!/$!', $site_url)) $site_url .= '/';
            $link = $site_url . $tmpl['template_outfile'];
            $_template_links[$cache_key] = $link;
        }
        if (!$args['with_index']) {
            $link = _strip_index($link, $blog);
        }
        return $link;
    } elseif (isset($args['entry_id'])) {
        $arg = array('entry_id' => $args['entry_id']);
        list($entry) = $ctx->mt->db->fetch_entries($arg);
        $ctx->localize(array('entry'));
        $ctx->stash('entry', $entry);
        $link = $ctx->tag('EntryPermalink',$args);
        $ctx->restore(array('entry'));
        if ($args['with_index'] && preg_match('/\/(#.*)$/', $link)) {
            $blog = $ctx->stash('blog');
            $index = $ctx->mt->config('IndexBasename');
            $ext = $blog['blog_file_extension'];
            if ($ext) $ext = '.' . $ext; 
            $index .= $ext;
            $link = preg_replace('/\/(#.*)?$/', "/$index\$1", $link);
        }
        return $link;
    }
    return '';
}