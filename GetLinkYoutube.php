<?php

class GetLinkYoutube
{
    protected $links;
    protected $directLinks;
    protected $listIds;
    protected $linksToText;

    public function extractPlaylistId($playlistLink)
    {
        $playlist = $playlistLink;
        $linkPart = explode("=", $playlist);
        $id = $linkPart[1];
        shell_exec("./playlist2links {$id}");
    }

    protected function getLinksFromFile()
    {
        return file('playlist.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    }

    protected function extractLink()
    {
        $links = $this->getLinksFromFile();
        foreach ($links as $link) {
            $linkParts = explode("=", $link);
            $listIds[] = $linkParts[1];
        }

        return $listIds;
    }

    protected function getDirectLinks()
    {
        $listIds = $this->extractLink();

        foreach ($listIds as $id) {
            $content = file_get_contents("http://www.youtube.com/get_video_info?video_id=$id&el=embedded&ps=default&eurl=&gl=US&hl=en");
            $contentParts = explode("&", $content);

            $data = [];
            $output = [];
            $processingData = [];

            foreach ($contentParts as $contentPart) {
                $elements = explode("=", $contentPart);
                $key = $elements[0];
                $value = $elements[1];
                $y = urldecode($value);
                $data[$key] = $value;
            }

            $streams = explode(',', urldecode($data['url_encoded_fmt_stream_map']));

            foreach ($streams as $stream) {
                $streamParts = explode('&', $stream);
                foreach ($streamParts as $streamPart) {
                    $elements = explode('=', $streamPart);
                    $key = $elements[0];
                    $value = $elements[1];
                    $processingData[$key] = urldecode($value);
                }
                $output[] = $processingData;
            }
            $this->directLinks[] = $output;
        }

        return $this->directLinks;
    }

    public function output()
    {
        $i = 1;

        $links = $this->getDirectLinks();
        foreach ($links as $link) {
            echo "<a href='{$link[1]['url']}'>link - {$i}</a><br>";
            $this->linksToText .= $link[0]['url'] . PHP_EOL;
            $i++;
        }
        file_put_contents('download.txt', $this->linksToText);
    }
}
