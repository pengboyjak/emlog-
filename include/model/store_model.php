<?php

/**
 * store model
 * @package EMLOG
 * @link https://www.emlog.net
 */

class Store_Model
{

    public function getApps($tag, $keyword, $page, $author_id, $sid)
    {
        return $this->reqEmStore('all', $tag, $keyword, $page, $author_id, $sid);
    }

    public function getTemplates($tag, $keyword, $page, $author_id, $sid)
    {
        return $this->reqEmStore('tpl', $tag, $keyword, $page, $author_id, $sid);
    }

    public function getPlugins($tag, $keyword, $page, $author_id, $sid)
    {
        return $this->reqEmStore('plu', $tag, $keyword, $page, $author_id, $sid);
    }

    public function getMyAddon()
    {
        return $this->reqEmStore('mine');
    }

    public function getSvipAddon()
    {
        return $this->reqEmStore('svip');
    }

    public function getTopAddon()
    {
        return $this->reqEmStore('top');
    }

    public function reqEmStore($type, $tag = '', $keyword = '', $page = 1, $author_id = 0, $sid = 0)
    {
        $emcurl = new EmCurl();

        $post_data = [
            'emkey'     => Option::get('emkey'),
            'ver'       => Option::EMLOG_VERSION,
            'type'      => $type,
            'tag'       => $tag,
            'keyword'   => $keyword,
            'page'      => $page,
            'author_id' => $author_id,
            'sid'       => $sid
        ];
        $emcurl->setPost($post_data);
        $emcurl->request('https://store.emlog.net/store/pro');

        $retStatus = $emcurl->getHttpStatus();
        if ($retStatus !== MSGCODE_SUCCESS) {
            emDirect("./store.php?action=error&error=1");
        }
        $response = $emcurl->getRespone();
        $ret = json_decode($response, 1);
        if (empty($ret)) {
            emDirect("./store.php?action=error&error=1");
        }
        if ($ret['code'] === MSGCODE_EMKEY_INVALID) {
            Option::updateOption('emkey', '');
            $CACHE = Cache::getInstance();
            $CACHE->updateCache('options');
            emDirect("./auth.php?error_store=1");
        }

        $data = [];
        switch ($type) {
            case 'all':
                $data['apps'] = isset($ret['data']['apps']) ? $ret['data']['apps'] : [];
                $data['count'] = isset($ret['data']['count']) ? $ret['data']['count'] : 0;
                $data['page_count'] = isset($ret['data']['page_count']) ? $ret['data']['page_count'] : 0;
                $data['has_more'] = isset($ret['has_more']) ? $ret['has_more'] : false;
                break;
            case 'tpl':
                $data['templates'] = isset($ret['data']['templates']) ? $ret['data']['templates'] : [];
                $data['count'] = isset($ret['data']['count']) ? $ret['data']['count'] : 0;
                $data['page_count'] = isset($ret['data']['page_count']) ? $ret['data']['page_count'] : 0;
                $data['has_more'] = isset($ret['has_more']) ? $ret['has_more'] : false;
                break;
            case 'plu':
                $data['plugins'] = isset($ret['data']['plugins']) ? $ret['data']['plugins'] : [];
                $data['count'] = isset($ret['data']['count']) ? $ret['data']['count'] : 0;
                $data['page_count'] = isset($ret['data']['page_count']) ? $ret['data']['page_count'] : 0;
                $data['has_more'] = isset($ret['has_more']) ? $ret['has_more'] : false;
                break;
            case 'svip':
            case 'mine':
            case 'top':
                $data = isset($ret['data']) ? $ret['data'] : [];
                break;
        }
        return $data;
    }
}
