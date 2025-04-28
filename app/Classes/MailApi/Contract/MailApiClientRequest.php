<?php

namespace App\Classes\MailApi\Contract;


class MailApiClientRequest implements \JsonSerializable
{
    protected $FromAsBase64;
    protected $ToAsBase64;
    protected $SubjectAsBase64;
    protected $BodyAsBase64;
    protected $IsBodyHtml;

    public function __construct($from, $to, $subject, $body, $isBodyHtml)
    {
        $this->FromAsBase64 = base64_encode($from);
        $this->ToAsBase64 = base64_encode($to);
        $this->SubjectAsBase64 = base64_encode($subject);
        $this->BodyAsBase64 = base64_encode($body);
        $this->IsBodyHtml = $isBodyHtml;
    }

    public function jsonSerialize() : array
    {
        return [
            'FromAsBase64' => $this->FromAsBase64,
            'ToAsBase64' => $this->ToAsBase64,
            'SubjectAsBase64' => $this->SubjectAsBase64,
            'BodyAsBase64' => $this->BodyAsBase64,
            'IsBodyHtml' => $this->IsBodyHtml,
        ];
    }
}
