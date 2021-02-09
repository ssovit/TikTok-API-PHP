<?php

namespace Sovit\TikTok;

if (!\class_exists('\Sovit\TikTok\ICacheEngine')) {
    interface ICacheEngine
    {
        public function get(string $cache_key);
        public function set(string $cache_key, array $data, int $timeout);
    }
}
