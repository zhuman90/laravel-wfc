<?php

/*
 * This file is part of the laravuel/laravel-wfc.
 *
 * (c) laravuel <45761113@qq.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Laravuel\LaravelWFC;

use Illuminate\Support\Facades\Cache;
use EasyWeChat\Factory;

class Collector
{
    private $openid;
	private $config;
    private $cache;
    private $cacheKey;

    public function __construct($openid, $config = []) 
    {
        $this->openid = $openid;
		$this->config = $config ? $config : config('wfc');
		$this->cache = Cache::store($this->config['cache_driver'] ? $this->config['cache_driver']: 'file');
        $this->cacheKey = $this->getCacheKey();	// 每个openid对应一个key
    }

    /**
     * 获取缓存key
     * 
     */
    public function getCacheKey() 
	{
        return $this->config['cache_prefix'].$this->openid;
    }
	
	/**
     * 发送模板消息
     * 
	 * @param $data 模板消息参数
     */
	public function send($data)
	{
		$mina = Factory::miniProgram([
            'app_id' => $this->config['app_id'],
            'secret' => $this->config['secret'],
        ]);
		// 获取一个可用的formId，然后删除掉
        $formId = $this->get(true);
		
        if (!$formId) {
            throw new \Exception('no formId');
        } else {
			$data['touser'] = $this->openid;
            $data['form_id'] = $formId;
			
			// 用overtrue/wechat包来发送模板消息
            $res = $mina->template_message->send($data);
            return $res;
        }
	}
	
	/**
     * 存储formId
     * 
	 * @param $formId
     */
    public function save($formId) 
	{
        $formIds = $this->gets();
        $formIds->push([
            'form_id' => $formId,
            'expire' => time() + $this->config['expire_second'] // formId过期时间
        ]);
		// 存储到redis缓存中
        $this->cache->forever($this->cacheKey, $formIds->toArray());
    }

    /**
     * 获取某个未过期的formId
	 *
     * @param $delete 获取之后是否立即删除
     */
    public function get($delete = false) 
	{
        $formIds = $this->gets();
        if (!$formIds->count()) {
            return false;
        }
		// 筛选一个有效的formId，优先获取快过期的
        $formId = $formIds->where('expire', '>=', time())->sortBy('expire')->first()['form_id'];
        if ($delete && $formId) {
            $this->delete($formId);
        }
        return $formId;
    }

    /**
     * 获取formId集合
     * 
     * @return \Illuminate\Support\Collection
     */
    public function gets() 
	{
        $formIds = $this->cache->get($this->cacheKey);
        return collect($formIds ? $formIds : []);
    }

    /**
     * 删除某个formId
     * 
	 * @param $formId
     */
    public function delete($formId) 
	{
        $formIds = $this->gets();
        $formIds = $formIds->filter(function($item) use($formId) {
            return $item['form_id'] != $formId;
        });
        $this->cache->forever($this->cacheKey, $formIds->toArray());
    }

    /**
     * 清理所有已过期的formId
     * 
     */
    public function clearExpireFormIds() 
	{
        $formIds = $this->gets();
        $time = time();
        $formIds = $formIds->filter(function($item) use($time) {
            return $item['expire'] > $time;
        });
        $this->cache->forever($this->cacheKey, $formIds->toArray());
    }
}