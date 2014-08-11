<?php
header('Content-Type:application/atom+xml');
$files = scandir('posts', 1);
$postfiles = array();
foreach ($files as $file) {
    if ($file[0] != '.') $postfiles[filemtime("posts/$file")] = $file;
}
krsort($postfiles);
?>
<?xml version="1.0" encoding="utf-8"?>
 
<feed xmlns="http://www.w3.org/2005/Atom">
 
        <title>Shobute</title>
        <subtitle>Latest updates from Shobute</subtitle>
        <link href="http://shobute.com/feed" rel="self" />
        <link href="http://shobute.com/" />
        <id>tag:shobute.com,2012:http://shobute.com/feed</id>
        <updated><?php echo date('Y-m-d\TH:i:s\Z', key($postfiles)); ?></updated>
 
<?php
        $i = 1;
        foreach ($postfiles as $t => $post) {
            $time[0] = date('Y-m-d\TH:i:s\Z', $t);
            $time[1] = date('Y-m-d', $t);
            $title = ucwords(str_replace('-', ' ', $post));
            $content = file_get_contents("posts/$post");
            $summary = strlen($content) > 100 ? substr(strip_tags($content), 0, 100) . '...' : $content;       echo "<entry>
                <title>$title</title>
                <link rel=\"alternate\" type=\"text/html\" href=\"http://shobute.com/posts/$post\"/>
                <id>tag:shobute.com,2012:http://shobute.com/feed?id=$post</id>
                <updated>$time[0]</updated>
                <summary type=\"html\">$summary</summary>
                <content type=\"html\"><![CDATA[$content]]></content>
                <author>
                      <name>Ben</name>
                      <email>shobute2@gmail.com</email>
                </author>
        </entry>";
            if (++$i > 128) break;    # number of posts to show
            }
?>

 
</feed>
