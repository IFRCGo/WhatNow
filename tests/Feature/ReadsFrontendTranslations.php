<?php

namespace Tests\Feature;

trait ReadsFrontendTranslations
{
    /**
     * @param $key
     * @return string
     */
    public function trans($key)
    {
        $lang = $this->app->getLocale();
        $translationContent = json_decode(file_get_contents(__DIR__."/../../resources/lang/$lang.json"), true);

        $keyParts = explode('.', $key);

        if (count($keyParts) === 1) {
            if (isset($translationContent[$keyParts[0]])) {
                return $translationContent[$key];
            } else {
                $this->fail('Translation not found');
            }
        }

        return $this->traverseArray($keyParts, $translationContent);
    }

    /**
     * @param $keys
     * @param $values
     * @return mixed
     */
    private function traverseArray($keys, $values)
    {
        try {
            $value = $values;
            foreach ($keys as $key) {
                $value = $value[$key];
            }

            return $value;
        } catch (\Exception $e) {
            $this->fail('Translation not found');
        }
    }
}
