<?php

    function flip_row_col_array($array) {
        $out = array();
        foreach ($array as  $rowkey => $row) {
            foreach($row as $colkey => $col){
                $out[$colkey][$rowkey]=$col;
            }
        }
        return $out;
    }

    if(!isset($_GET['vid-id']))
    {
        http_response_code(400);
        return;
    }

    // get video id from url
    $video_url = 'https://www.youtube.com/watch?v=' . $_GET['vid-id'];
    preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $video_url, $matches);

    // get video info from id
    $video_id = $matches[0];
    $video_info = file_get_contents('https://www.youtube.com/get_video_info?&video_id='.$video_id);
    parse_str($video_info, $video_info_array);
    
    preg_match('/(?<="playerCaptionsTracklistRenderer":).*(?=},"videoDetails":)/', urldecode($video_info), $matches);

    $vid_info_obj = json_decode($matches[0], true);
    $captionURL = $vid_info_obj["captionTracks"][0]["baseUrl"];

    $transcript = file_get_contents($captionURL);
    $transcript = html_entity_decode($transcript);

    preg_match_all('/(?<=<text start=")(\d+\.\d+)" dur="(\d+\.\d+)">([\s\S]*?)(?=<\/text>)/m', $transcript, $matches);

    $matches = flip_row_col_array($matches);

    $first = true;
    foreach($matches as $match) {
        if(!$first)
            echo "\n\n";
        else
            $first = false;
        
        echo $match[1] . ',' . $match[2] . "\n" . $match[3];
    }

?>