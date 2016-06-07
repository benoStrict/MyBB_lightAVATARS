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

if(!defined("IN_MYBB")) {
    exit;
}

$plugins->hooks['pre_output_page'][5]['LightAvatars->getAvatars']=[
    'class_method' => ['LightAvatars', 'getAvatars']
];

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

function lightavatars_activate() {
    global $db, $cache, $lang, $mybb;
    $lang->load('config_lightavatars');

    $sgid=$db->insert_query(
            "settinggroups", 
            [
                "name"=>"lightavatars", 
                "title"=>"LightAVATARS", 
                "description"=>$db->escape_string($lang->setting_group_lightavatars_desc)
            ]);
    
    $avatarview='
    <div class="lavatar">
        <a href="#" title="username" rel="nofollow" id="la_link" class="">
            <img src="'.$mybb->settings['bburl'].'/'.$mybb->settings['useravatar'].'" id="la_img" class="">
        </a>
    </div><script src="'.$mybb->settings['bburl'].'/resources/settings.js"></script>'.$lang->lightavatars_custom_desc;

    $sg=[
        [
            'name'=>'lightavatars_view', 
            'title'=>$lang->lightavatars_view, 
            'description'=>$avatarview, 
            'optionscode'=>'', 
            'value'=>''
            ],
        [
            'name'=>'lightavatars_custom', 
            'title'=>$lang->lightavatars_custom, 
            'description'=>$lang->lightavatars_custom_desc, 
            'optionscode'=>'yesno', 
            'value'=>'0'
            ],
        [
            'name'=>'lightavatars_link', 
            'title'=>$lang->lightavatars_link, 
            'description'=>$lang->lightavatars_link_desc, 
            'optionscode'=>'text', 
            'value'=>'normal'
            ],
        [
            'name'=>'lightavatars_img', 
            'title'=>$lang->lightavatars_img, 
            'description'=>$lang->lightavatars_img_desc, 
            'optionscode'=>'text', 
            'value'=>'normal'
            ],
        [
            'name'=>'lightavatars_forumbit_depth2_forum_lastpost', 
            'title'=>$lang->lightavatars_forumbit_depth2_forum_lastpost, 
            'description'=>$lang->lightavatars_forumbit_depth2_forum_lastpost_desc, 
            'optionscode'=>'text', 
            'value'=>'normal'
            ],
        [
            'name'=>'lightavatars_forumbit_depth1_forum_lastpost', 
            'title'=>$lang->lightavatars_forumbit_depth1_forum_lastpost, 
            'description'=>$lang->lightavatars_forumbit_depth1_forum_lastpost_desc, 
            'optionscode'=>'text', 
            'value'=>'normal'
            ],
        [
            'name'=>'lightavatars_forumdisplay_thread_firstpost', 
            'title'=>$lang->lightavatars_forumdisplay_thread_firstpost, 
            'description'=>$lang->lightavatars_forumdisplay_thread_firstpost_desc, 
            'optionscode'=>'text', 
            'value'=>'normal'
            ],
        [
            'name'=>'lightavatars_forumdisplay_thread_lastpost', 
            'title'=>$lang->lightavatars_forumdisplay_thread_lastpost, 
            'description'=>$lang->lightavatars_forumdisplay_thread_lastpost_desc, 
            'optionscode'=>'text', 
            'value'=>'normal'
            ],
        [
            'name'=>'lightavatars_search_results_posts_post', 
            'title'=>$lang->lightavatars_search_results_posts_post, 
            'description'=>$lang->lightavatars_search_results_posts_post_desc, 
            'optionscode'=>'text', 
            'value'=>'normal'
            ],
        [
            'name'=>'lightavatars_search_results_threads_thread_firstpost', 
            'title'=>$lang->lightavatars_search_results_threads_thread_firstpost, 
            'description'=>$lang->lightavatars_search_results_threads_thread_firstpost_desc, 
            'optionscode'=>'text', 
            'value'=>'normal'
            ],
        [
            'name'=>'lightavatars_search_results_threads_thread_lastpost', 
            'title'=>$lang->lightavatars_search_results_threads_thread_lastpost, 
            'description'=>$lang->lightavatars_search_results_threads_thread_lastpost_desc, 
            'optionscode'=>'text', 
            'value'=>'normal'
            ]
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
    $styles=file_get_contents(__DIR__.'/../../resources/lightavatars.css');
    
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
            'forumdisplay_thread', 
            '#'.preg_quote('{$attachment_count}').'#', 
            '{+LIGHTAVATARS+}forumdisplay_thread_firstpost|{$thread[\'uid\']}{+ENDofBLOCK+}{$attachment_count}'
            );
    
    find_replace_templatesets(
            'forumdisplay_thread', 
            '/<(.*?)(\s*?){\$lastpostdate}/', 
            '{+LIGHTAVATARS+}forumdisplay_thread_lastpost|{$thread[\'lastposteruid\']}{+ENDofBLOCK+}<\\1\\2{$lastpostdate}'
            );
    
    find_replace_templatesets(
            'forumbit_depth1_forum_lastpost', 
            '#\A#', 
            '{+LIGHTAVATARS+}forumbit_depth1_forum_lastpost|{$lastpost_data[\'lastposteruid\']}{+ENDofBLOCK+}'
            );
    
    find_replace_templatesets(
            'forumbit_depth2_forum_lastpost', 
            '#\A#', 
            '{+LIGHTAVATARS+}forumbit_depth2_forum_lastpost|{$lastpost_data[\'lastposteruid\']}{+ENDofBLOCK+}'
            );
    
    find_replace_templatesets(
            'search_results_posts_post', 
            '#'.preg_quote('{$post[\'profilelink\']}').'#', 
            '{+LIGHTAVATARS+}search_results_posts_post|{$post[\'uid\']}{+ENDofBLOCK+}{$post[\'profilelink\']}'
            );
    
    find_replace_templatesets(
            'search_results_threads_thread', 
            '#'.preg_quote('{$attachment_count}').'#', 
            '{+LIGHTAVATARS+}search_results_threads_thread_firstpost|{$thread[\'uid\']}{+ENDofBLOCK+}{$attachment_count}'
            );
    
    find_replace_templatesets(
            'search_results_threads_thread', 
            '/<(.*?)(\s*?){\$lastpostdate}/', 
            '{+LIGHTAVATARS+}forumdisplay_thread_lastpost|{$thread[\'lastposteruid\']}{+ENDofBLOCK+}<\\1\\2{$lastpostdate}'
            );
}

function lightavatars_deactivate() {
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
    $deletepattern='#\{\+LIGHTAVATARS\+\}(.*?)\{\+ENDofBLOCK\+\}#';
    find_replace_templatesets(
            'forumdisplay_thread', 
            $deletepattern, 
            ''
            );
    find_replace_templatesets(
            'forumdisplay_thread', 
            $deletepattern, 
            '' 
            );
    find_replace_templatesets(
            'forumbit_depth1_forum_lastpost', 
            $deletepattern, 
            ''
            );
    find_replace_templatesets(
            'forumbit_depth2_forum_lastpost', 
            $deletepattern, 
            ''
            );
    find_replace_templatesets(
            'search_results_posts_post', 
            $deletepattern, 
            ''
            );
    find_replace_templatesets(
            'search_results_threads_thread', 
            $deletepattern, 
            ''
            );
    find_replace_templatesets(
            'search_results_threads_thread', 
            $deletepattern, 
            ''
            );
}

class LightAvatars
{ 
    
    public function getAvatars(&$content)
    {
        global $db, $mybb;
        $matches=[];
        preg_match_all('/\{\+LIGHTAVATARS\+\}(.*?)\|(.*?)\{\+ENDofBLOCK\+\}/', $content, $matches);
        if(!$matches[0]) {
            return $content;
        }
        $matchescount=count($matches[0]);
        
        while($matchescount--) {
            $info[$matches[2][$matchescount]]['position'][$matches[1][$matchescount]]=1;
            if($matches[2][$matchescount]!=0) {
                $select[$matches[2][$matchescount]]=$matches[2][$matchescount];
            } else {
                $info[0]['avatar']='./'.$mybb->settings['useravatar'];
                $info[0]['name']='unregistered';
            }
        }
        if(isset($select)) {
            $selected='uid='.implode(' OR uid=',$select);
            $avatardata=$db->simple_select(
                'users', 
                'uid,username,avatar', 
                $selected, 
                NULL
                );
        }
        while($base=$db->fetch_array($avatardata)) {
            $info[$base['uid']]['avatar']=$base['avatar'];
            $info[$base['uid']]['name']=$base['username'];
        }
        
        foreach($info as $key => $avatar){
            if($mybb->settings['lightavatars_custom']) {
+                $style['avatar']=' lavatar--'.$avatar['name'];
+                $style['link']=' lavatar__link--'.$avatar['name'];
+                $style['img']=' lavatar__img--'.$avatar['name'];
            }
            
            $masterstyle['link']=explode(' ',$mybb->settings['lightavatars_link']);
            $masterstyle['link']=" lavatar__link--".implode(" lavatar__link--",$masterstyle['link']);
            $masterstyle['img']=explode(' ',$mybb->settings['lightavatars_img']);
            $masterstyle['img']=" lavatar__img--".implode(" lavatar__img--",$masterstyle['img']);
            
            $avatargen='';
            if(empty($avatar['avatar'])) {
                $avatar['avatar']='./'.$mybb->settings['useravatar'];
            }
            $avatargen='<img src="'.$avatar['avatar'].'" alt="'.$mybb->settings['bbname'].' user avatar image" class="lavatar__img'.$masterstyle['img'].$style['img'].'" onError="this.src=\''.$mybb->settings['bburl'].'/'.$mybb->settings['useravatar'].'\';">'; 
            if($key!==0) {
                $avatargen='<a href="'.$mybb->settings['bburl'].'/'.get_profile_link($key).'" title="'.$avatar['name'].'" rel="nofollow" class="lavatar__link'.$masterstyle['link'].$style['link'].'">'.$avatargen.'</a>';
            }
            
            foreach($avatar['position'] as $position => $truevalue) {
                $masterstyle['avatar']=explode(' ',$mybb->settings['lightavatars_'.$position]);
                $masterstyle['avatar']=" lavatar--".implode(" lavatar--",$masterstyle['avatar']);
                $content=str_replace('{+LIGHTAVATARS+}'.$position.'|'.$key.'{+ENDofBLOCK+}', '<div class="lavatar'.$masterstyle['avatar'].$style['avatar'].'">'.$avatargen.'</div>', $content);
            }
        }
        return $content;
    }
}
