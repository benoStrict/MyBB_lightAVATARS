<?php

/* 
 * The MIT License
 *
 * Copyright 2016 Arthur.
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
    global $lang, $db;
    $lang->load('config_lightavatars');
    
    $query = $db->simple_select('settinggroups', 'gid', "name='lightavatars'");
    $gid = $db->fetch_field($query, 'gid');
    if($gid) {
        $linktodesc=$lang->lightavatars_desc.'<br><strong><a href="index.php?module=config-settings&amp;action=change&amp;gid='.$gid.'">'.$lang->lightavatars_shortcut.'</a></strong>';
    } else {
        $linktodesc=$lang->lightavatars_desc;
    }
    
    return [
        "name" => "LightAVATARS",
        "description" => $linktodesc,
        "website" => "",
        "author" => "KICek",
        "authorsite" => "",
        "version" => "0.9.9",
        "codename" => "LastPosterAvatarLight",
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
                "description"=>$db->escape_string($lang->lightavatars_desc)
            ]);
    
    $avatarview='
    <div id="la_avatar">
        <a href="#" title="username" rel="nofollow">
            <img src="'.$mybb->settings['bburl'].'/resources/la_tester.png">
        </a>
    </div><script src="'.$mybb->settings['bburl'].'/resources/settings.js"></script>';

    $sg=[
        [
            'name'=>'lightavatars_view', 
            'title'=>$lang->lightavatars_view, 
            'description'=>$avatarview, 
            'optionscode'=>'select \n 1=setting_lightavatars_forumbit_depth2_forum_lastpost \n 2=setting_lightavatars_forumbit_depth1_forum_lastpost \n 3=setting_lightavatars_forumdisplay_thread_firstpost \n 4=setting_lightavatars_forumdisplay_thread_lastpost \n 5=setting_lightavatars_search_results_posts_post \n 6=setting_lightavatars_search_results_threads_thread_firstpost \n 7=setting_lightavatars_search_results_threads_thread_lastpost \n 8=setting_lightavatars_private_messagebit \n 9=setting_lightavatars_forumdisplay_announcements_announcement \n', 
            'value'=>'1'
            ],
        [
            'name'=>'lightavatars_custom', 
            'title'=>$lang->lightavatars_custom, 
            'description'=>$lang->lightavatars_custom_desc, 
            'optionscode'=>'yesno', 
            'value'=>'0'
            ],
        [
            'name'=>'lightavatars_forumbit_depth2_forum_lastpost', 
            'title'=>$lang->lightavatars_forumbit_depth2_forum_lastpost, 
            'description'=>"", 
            'optionscode'=>'text', 
            'value'=>'old'
            ],
        [
            'name'=>'lightavatars_forumbit_depth1_forum_lastpost', 
            'title'=>$lang->lightavatars_forumbit_depth1_forum_lastpost, 
            'description'=>"", 
            'optionscode'=>'text', 
            'value'=>'old'
            ],
        [
            'name'=>'lightavatars_forumdisplay_thread_firstpost', 
            'title'=>$lang->lightavatars_forumdisplay_thread_firstpost, 
            'description'=>"", 
            'optionscode'=>'text', 
            'value'=>'old'
            ],
        [
            'name'=>'lightavatars_forumdisplay_thread_lastpost', 
            'title'=>$lang->lightavatars_forumdisplay_thread_lastpost, 
            'description'=>"", 
            'optionscode'=>'text', 
            'value'=>'old'
            ],
        [
            'name'=>'lightavatars_search_results_posts_post', 
            'title'=>$lang->lightavatars_search_results_posts_post, 
            'description'=>"", 
            'optionscode'=>'text', 
            'value'=>'old'
            ],
        [
            'name'=>'lightavatars_search_results_threads_thread_firstpost', 
            'title'=>$lang->lightavatars_search_results_threads_thread_firstpost, 
            'description'=>"", 
            'optionscode'=>'text', 
            'value'=>'old'
            ],
        [
            'name'=>'lightavatars_search_results_threads_thread_lastpost', 
            'title'=>$lang->lightavatars_search_results_threads_thread_lastpost, 
            'description'=>"", 
            'optionscode'=>'text', 
            'value'=>'old'
            ],
        [
            'name'=>'lightavatars_private_messagebit', 
            'title'=>$lang->lightavatars_private_messagebit, 
            'description'=>"", 
            'optionscode'=>'text', 
            'value'=>'old'
            ],
        [
            'name'=>'lightavatars_forumdisplay_announcements_announcement', 
            'title'=>$lang->lightavatars_forumdisplay_announcements_announcement, 
            'description'=>"", 
            'optionscode'=>'text', 
            'value'=>'old'
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
                "attachedto"=>"",
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
            '{+}forumdisplay_thread_firstpost|{$thread[\'uid\']}{-}{$attachment_count}'
            );
    
    find_replace_templatesets(
            'forumdisplay_thread', 
            '/<(.*?)(\s*?){\$lastpostdate}/', 
            '{+}forumdisplay_thread_lastpost|{$thread[\'lastposteruid\']}{-}<\\1\\2{$lastpostdate}'
            );
    
    find_replace_templatesets(
            'forumbit_depth1_forum_lastpost', 
            '#\A#', 
            '{+}forumbit_depth1_forum_lastpost|{$lastpost_data[\'lastposteruid\']}{-}'
            );
    
    find_replace_templatesets(
            'forumbit_depth2_forum_lastpost', 
            '#\A#', 
            '{+}forumbit_depth2_forum_lastpost|{$lastpost_data[\'lastposteruid\']}{-}'
            );
    
    find_replace_templatesets(
            'search_results_posts_post', 
            '#'.preg_quote('{$post[\'profilelink\']}').'#', 
            '{+}search_results_posts_post|{$post[\'uid\']}{-}{$post[\'profilelink\']}'
            );
    
    find_replace_templatesets(
            'search_results_threads_thread', 
            '#'.preg_quote('{$attachment_count}').'#', 
            '{+}search_results_threads_thread_firstpost|{$thread[\'uid\']}{-}{$attachment_count}'
            );
    
    find_replace_templatesets(
            'search_results_threads_thread', 
            '/<(.*?)(\s*?){\$lastpostdate}/', 
            '{+}forumdisplay_thread_lastpost|{$thread[\'lastposteruid\']}{-}<\\1\\2{$lastpostdate}'
            );
    find_replace_templatesets(
            'private_messagebit', 
            '#'.preg_quote('{$tofromusername}').'#',
            '{+}private_messagebit|{$tofromuid}{-}{$tofromusername}'
            );
    find_replace_templatesets(
            'forumdisplay_announcements_announcement', 
            '#<(.*?)'.preg_quote('{$announcement[\'subject\']}').'#',
            '{+}forumdisplay_announcements_announcement|{$announcement[\'fid\']}{-}<\\1{$announcement[\'subject\']}'
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
    $deletepattern='#\{\+\}(.*?)\{-\}#';
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
            'private_messagebit', 
            $deletepattern, 
            ''
            );
    find_replace_templatesets(
            'forumdisplay_announcements_announcement', 
            $deletepattern, 
            ''
            );
}

class LightAvatars
{ 
    
    public function getAvatars(&$content)
    {
        global $db, $mybb;
        
        //input
        preg_match_all('/\{\+\}(.*?)\|(.*?)\{\-\}/', $content, $matches);
        if(!$matches[0]) {
            return $content;
        }
        
        //getvariables
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
            while($base=$db->fetch_array($avatardata)) {
                $info[$base['uid']]['avatar']=$base['avatar'];
                $info[$base['uid']]['name']=$base['username'];
            }
        }
        
        foreach($info as $key => $avatar) {
            
            if(empty($avatar['avatar'])) {
                $avatar['avatar']='./'.$mybb->settings['useravatar'];
            }
            
            if($key!==0) {
                $avatargen='<a href="'.$mybb->settings['bburl'].'/'.get_profile_link($key).'" title="'.$avatar['name'].'" rel="nofollow"><img src="'.$avatar['avatar'].'" alt="'.$mybb->settings['bbname'].'" onError="this.src=\''.$mybb->settings['bburl'].'/'.$mybb->settings['useravatar'].'\';"></a>';
            } else {
                $avatargen='<a><img src="'.$avatar['avatar'].'" alt="'.$mybb->settings['bbname'].'" onError="this.src=\''.$mybb->settings['bburl'].'/'.$mybb->settings['useravatar'].'\';"></a>';
            }
            
            foreach($avatar['position'] as $position => $truevalue) {
                if(!$masterstyle[$position]) {
                    $masterstyle[$position]=explode(' ',$mybb->settings['lightavatars_'.$position]);
                    $masterstyle[$position]="lavatar-".implode(" lavatar-",$masterstyle[$position]);
                }
                if($mybb->settings['lightavatars_custom']) {
                    $style['avatar']=' lavatar-'.$avatar['name'].'-'.$position;
                }
                $content=str_replace('{+}'.$position.'|'.$key.'{-}', '<div class="'.$masterstyle[$position].$style['avatar'].'">'.$avatargen.'</div>', $content);
            }
        }
        return $content;
    }
}
