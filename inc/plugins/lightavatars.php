<?php

/* 
 * The MIT License
 *
 * Copyright 2016 MiArz.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

//

if(!defined("IN_MYBB")) {
    exit;
}

$plugins->add_hook(
        'pre_output_page', 
        ['LightAvatars', 'getAvatars']
        );

function lightavatars_info() {
    global $lang;
    $lang->load('config_lightavatars');
    
    return [
        "name" => "LightAVATARS",
        "description" => $lang->lightavatars_desc,
        "website" => "",
        "author" => "MiArz",
        "authorsite" => "",
        "version" => "0.9",
        "codename" => "LightAvatars",
        "compatibility" => "18*"
    ];
}

function lightavatars_is_installed() {
    global $db;
    return $db->num_rows(
            $db->simple_select(
                    'settinggroups', 
                    '*', 
                    'name="lightavatars"'
                    )
            );
}

function lightavatars_install() {
    global $db, $cache, $lang;
    $lang->load('config_lightavatars');

    $sgid=$db->insert_query(
            "settinggroups", 
            [
                "name"=>"lightavatars", 
                "title"=>"LightAVATARS", 
                "description"=>$db->escape_string($lang->setting_group_lightavatars_desc)
            ]);

    $sg=[
        [
            'name'=>'lightavatars_custom', 
            'title'=>$lang->lightavatars_custom, 
            'description'=>$lang->lightavatars_custom_desc, 
            'optionscode'=>'yesno', 
            'value'=>'0'
            ],
        [
            'name'=>'lightavatars_block', 
            'title'=>$lang->lightavatars_block, 
            'description'=>$lang->lightavatars_block_desc, 
            'optionscode'=>'text', 
            'value'=>'sqare normal'
            ],
        [
            'name'=>'lightavatars_link', 
            'title'=>$lang->lightavatars_link, 
            'description'=>$lang->lightavatars_link_desc, 
            'optionscode'=>'text', 
            'value'=>'inherit'
            ],
        [
            'name'=>'lightavatars_img', 
            'title'=>$lang->lightavatars_img, 
            'description'=>$lang->lightavatars_img_desc, 
            'optionscode'=>'text', 
            'value'=>'inherit'
            ],
        [
            'name'=>'lightavatars_forumbit_depth2_forum_lastpost', 
            'title'=>$lang->lightavatars_forumbit_depth2_forum_lastpost, 
            'description'=>$lang->lightavatars_forumbit_depth2_forum_lastpost_desc, 
            'optionscode'=>'text', 
            'value'=>'small'
            ],
        [
            'name'=>'lightavatars_forumbit_depth1_forum_lastpost', 
            'title'=>$lang->lightavatars_forumbit_depth1_forum_lastpost, 
            'description'=>$lang->lightavatars_forumbit_depth1_forum_lastpost_desc, 
            'optionscode'=>'text', 
            'value'=>'small'
            ],
        [
            'name'=>'lightavatars_forumdisplay_thread_firstpost', 
            'title'=>$lang->lightavatars_forumdisplay_thread_firstpost, 
            'description'=>$lang->lightavatars_forumdisplay_thread_firstpost_desc, 
            'optionscode'=>'text', 
            'value'=>'small'
            ],
        [
            'name'=>'lightavatars_forumdisplay_thread_lastpost', 
            'title'=>$lang->lightavatars_forumdisplay_thread_lastpost, 
            'description'=>$lang->lightavatars_forumdisplay_thread_lastpost_desc, 
            'optionscode'=>'text', 
            'value'=>'small'
            ],
        [
            'name'=>'lightavatars_search_results_posts_post', 
            'title'=>$lang->lightavatars_search_results_posts_post, 
            'description'=>$lang->lightavatars_search_results_posts_post_desc, 
            'optionscode'=>'text', 
            'value'=>'small'
            ],
        [
            'name'=>'lightavatars_search_results_threads_thread_firstpost', 
            'title'=>$lang->lightavatars_search_results_threads_thread_firstpost, 
            'description'=>$lang->lightavatars_search_results_threads_thread_firstpost_desc, 
            'optionscode'=>'text', 
            'value'=>'small'
            ],
        [
            'name'=>'lightavatars_search_results_threads_thread_lastpost', 
            'title'=>$lang->lightavatars_search_results_threads_thread_lastpost, 
            'description'=>$lang->lightavatars_search_results_threads_thread_lastpost_desc, 
            'optionscode'=>'text', 
            'value'=>'small'
            ],
        ];

    $i=1;
    foreach ($sg as &$row) {
        $row['gid']=$sgid;
        $row['title']=$db->escape_string($row['title']);
        $row['description']=$db->escape_string($row['description']);
        $row['disporder']=$i++;

    }
    
    $db->insert_query_multiple('settings', $sg);
    rebuild_settings();
    
    $db->delete_query(
            'themestylesheets', 
            'name="lightavatars.css" AND tid=1'
            );
    $styles=require_once __DIR__.'/../../resources/lightavatars.css';
    
    $db->insert_query(
            "themestylesheets", 
            [
                "name"=>"lightavatars.css",
                "cachefile"=>"lightavatars.css",
                "tid"=>"1",
                "attachedto"=>"forumdisplay.php|index.php|search.php",
                "stylesheet"=>$db->escape_string($styles),
                "lastmodified"=>TIME_NOW
            ]
            );
    
    require_once MYBB_ADMIN_DIR."inc/functions_themes.php";
    cache_stylesheet(1,"lightavatars.css",$styles);
    update_theme_stylesheet_list(1);
    
    require_once MYBB_ROOT.'inc/adminfunctions_templates.php';
    find_replace_templatesets(
            "forumdisplay_thread", 
            '#'.preg_quote('{$attachment_count}').'#', 
            '{%GUARD%}forumdisplay_thread_firstpost{%ENDofGUARD%}{%AVATAR%}{$thread[\'profilelink\']}{%ENDofAVATAR%}{$attachment_count}'
            );
    
    find_replace_templatesets(
            "forumdisplay_thread", 
            '#'.preg_quote('<span class="lastpost smalltext">').'(\s*?)'.preg_quote('{$lastpostdate}').'#', 
            '{%GUARD%}forumdisplay_thread_lastpost{%ENDofGUARD%}{%AVATAR%}{$lastposterlink}{%ENDofAVATAR%}<span class="lastpost smalltext">\\1{$lastpostdate}'
            );
    
    find_replace_templatesets(
            "forumbit_depth1_forum_lastpost", 
            '#'.preg_quote('<span class="smalltext">').'(\s*?)'.preg_quote('<a href="{$lastpost_link}"').'#', 
            '{%GUARD%}forumbit_depth1_forum_lastpost{%ENDofGUARD%}{%AVATAR%}{$lastpost_profilelink}{%ENDofAVATAR%}<span class="smalltext">\\1<a href="{$lastpost_link}"'
            );
    
    find_replace_templatesets(
            "forumbit_depth2_forum_lastpost", 
            '#'.preg_quote('<span class="smalltext">').'(\s*?)'.preg_quote('<a href="{$lastpost_link}"').'#', 
            '{%GUARD%}forumbit_depth2_forum_lastpost{%ENDofGUARD%}{%AVATAR%}{$lastpost_profilelink}{%ENDofAVATAR%}<span class="smalltext">\\1<a href="{$lastpost_link}"' 
            );
    
    find_replace_templatesets(
            "search_results_posts_post", 
            '#'.preg_quote('{$post[\'profilelink\']}').'#', 
            '{%GUARD%}search_results_posts_post{%ENDofGUARD%}{%AVATAR%}{$post[\'profilelink\']}{%ENDofAVATAR%}{$post[\'profilelink\']}'
            );
    
    find_replace_templatesets(
            "search_results_threads_thread", 
            '#'.preg_quote('{$attachment_count}').'#', 
            '{%GUARD%}search_results_threads_thread_firstpost{%ENDofGUARD%}{%AVATAR%}{$thread[\'profilelink\']}{%ENDofAVATAR%}{$attachment_count}'
            );
    
    find_replace_templatesets(
            "search_results_threads_thread", 
            '#'.preg_quote('<span class="smalltext">').'(\s*?)'.preg_quote('{$lastpostdate}').'#', 
            '{%GUARD%}search_results_threads_thread_lastpost{%ENDofGUARD%}{%AVATAR%}{$lastposterlink}{%ENDofAVATAR%}<span class="smalltext">\\1{$lastpostdate}'
            );
}

function lightavatars_uninstall() {
    global $db, $cache;
    $db->delete_query(
            'themestylesheets', 
            'name="lightavatars.css" AND tid=1'
            );
    
    require_once MYBB_ADMIN_DIR."inc/functions_themes.php";
    @unlink(MYBB_ROOT."cache/themes/theme1/lightavatars.css");
    @unlink(MYBB_ROOT."cache/themes/theme1/lightavatars.min.css");
    update_theme_stylesheet_list(1);
    
    $db->delete_query("settinggroups", "name=\"lightavatars\"");
    $db->delete_query("settings", "name LIKE \"lightavatars%\"");
    rebuild_settings();
    
    require_once MYBB_ROOT.'inc/adminfunctions_templates.php';
    find_replace_templatesets("forumdisplay_thread",'#'.preg_quote('{%GUARD%}').'(.*?)'.preg_quote('{%ENDofGUARD%}{%AVATAR%}{$thread[\'profilelink\']}{%ENDofAVATAR%}').'#','');
    find_replace_templatesets("forumdisplay_thread",'#'.preg_quote('{%GUARD%}').'(.*?)'.preg_quote('{%ENDofGUARD%}{%AVATAR%}{$lastposterlink}{%ENDofAVATAR%}').'#','');
    find_replace_templatesets("forumbit_depth1_forum_lastpost",'#'.preg_quote('{%GUARD%}').'(.*?)'.preg_quote('{%ENDofGUARD%}{%AVATAR%}{$lastpost_profilelink}{%ENDofAVATAR%}').'#','');
    find_replace_templatesets("forumbit_depth2_forum_lastpost",'#'.preg_quote('{%GUARD%}').'(.*?)'.preg_quote('{%ENDofGUARD%}{%AVATAR%}{$lastpost_profilelink}{%ENDofAVATAR%}').'#','');
    find_replace_templatesets("search_results_posts_post",'#'.preg_quote('{%GUARD%}').'(.*?)'.preg_quote('{%ENDofGUARD%}{%AVATAR%}{$post[\'profilelink\']}{%ENDofAVATAR%}').'#', '');
    find_replace_templatesets("search_results_threads_thread",'#'.preg_quote('{%GUARD%}').'(.*?)'.preg_quote('{%ENDofGUARD%}{%AVATAR%}{$thread[\'profilelink\']}{%ENDofAVATAR%}').'#','');
    find_replace_templatesets("search_results_threads_thread",'#'.preg_quote('{%GUARD%}').'(.*?)'.preg_quote('{%ENDofGUARD%}{%AVATAR%}{$lastposterlink}{%ENDofAVATAR%}').'#','');
}

final class LightAvatars
{ 
    
    public function getAvatars(&$content)
    {
        global $db, $mybb;
        
        $guards=[];
        $guardpattern='#{%GUARD%}(.*?){%ENDofGUARD%}#';
        preg_match_all($guardpattern, $content, $guards);
        
        
        $matches=[];
        $pattern='#{%AVATAR%}(.*?){%ENDofAVATAR%}#';
        preg_match_all($pattern, $content, $matches);
        
        $toreplace=$matches[0];
        
        $patternn='#<a(.*?)href="(.*?)"(.*?)>(.*?)</a>#';
        $array=preg_replace($patternn, '\\2---\\4', $matches[1]);
        
        unset($matches);
        
        foreach($array as $info) {
            $matches[]=explode('---', $info);
        }
        
        foreach($matches as $key=>$row) {
            $matches[$key][]=explode('uid=', $row[0])[1];
        }
        
        $i=count($toreplace);
        while($i--) {
            $content=str_replace($toreplace[$i],'{%AVATAR%}'.$matches[$i][2].'{%ENDofAVATAR%}',$content);
            $match[]=$matches[$i][2];
        }
        
        unset($array);
        
        $i=count($matches);
        while($i--) {
            if(!isset($matches[$i][2]))$matches[$i][2]=0;
            $array[$matches[$i][2]]=[
                $matches[$i][0],
                $matches[$i][1],
                $guards[1][$i]
            ];
        }
        
        foreach($matches as $info) {
            $array[$info[2]]=[
                $info[0], 
                $info[1]
                    ];
        }
        
        foreach(array_keys($array) as $key) {
            if(!empty($key))
                $select[]=$key;
        }
        
        $selected=implode(" OR ",$select);
        
        $avatardata=$db->simple_select(
                "users", 
                "uid,avatar", 
                $selected, 
                NULL
                );
        
        
        while($base=$db->fetch_array($avatardata)){
            
            if($mybb->settings['lightavatars_custom']) {
                $style['avatar']=' avatar';
                $style['block']=' avatar__block avatar_block--'.$array[$base['uid']][1];
                $style['link']=' avatar__link avatar__link--'.$array[$base['uid']][1];
                $style['img']=' avatar__img avatar__img--'.$array[$base['uid']][1];
            }
            
            if(!empty($mybb->settings['lightavatars_block'])){ 
                $masterstyle['block']=explode(' ',$mybb->settings['lightavatars_block']);
                $masterstyle['block']="avatar__block--".implode(" avatar__block--",$masterstyle['block']);
            }
            
            if(!empty($mybb->settings['lightavatars_link'])){ 
                $masterstyle['link']=explode(' ',$mybb->settings['lightavatars_link']);
                $masterstyle['link']="avatar__link--".implode(" avatar--__link",$masterstyle['link']);
            }
            
            if(!empty($mybb->settings['lightavatars_img'])){ 
                $masterstyle['img']=explode(' ',$mybb->settings['lightavatars_img']);
                $masterstyle['img']="avatar__img--".implode(" avatar--__img",$masterstyle['img']);
            }
            
            
            $avatars[$base['uid']]='<div class="'.$masterstyle['block'].$style['block'].'"><a href="'.$array[$base['uid']][0].'" class="'.$masterstyle['link'].$style['link'].'" title="'.$array[$base['uid']][1].'" rel="nofollow"><img src="';
            if($base['avatar']) {$avatars[$base['uid']].=$base['avatar'];} else {$avatars[$base['uid']].=$mybb->settings['useravatar'];}
            $avatars[$base['uid']].='" alt="'.$array[$base['uid']][1].' avatar image" class="'.$masterstyle['img'].$style['img'].'" onerror="this.src=\''.$mybb->settings['useravatar'].'\'"/></a></div></div>';
            
        }
        $avatars[0]='<div class="'.$masterstyle['block'].' avatar__block"><img src="'.$mybb->settings['useravatar'].'" alt="default avatar image" class="'.$masterstyle['img'].' avatar__img" onerror="this.src=\''.$mybb->settings['useravatar'].'\'"/></div></div>';
        
        $i=count($avatars);
        while($i--) {
            if(!isset($avatars[$match[$i]]))$avatars[$match[$i]]=$avatars[0];
            $content=str_replace(
                    '{%AVATAR%}'.$match[$i].'{%ENDofAVATAR%}', 
                    $avatars[$match[$i]], 
                    $content);
        }
        $i=count($guards[1]);
        while($i--) {
            $masterstyle['avatar']=explode(' ',$mybb->settings['lightavatars_'.$guards[1][$i]]);
            $masterstyle['avatar']="avatar--".implode(" avatar--",$masterstyle['avatar']);
            $content=str_replace(
                    '{%GUARD%}'.$guards[1][$i].'{%ENDofGUARD%}', 
                    '<div class='.$masterstyle['avatar'].$style['avatar'].'>', 
                    $content);
        }
    }
}
