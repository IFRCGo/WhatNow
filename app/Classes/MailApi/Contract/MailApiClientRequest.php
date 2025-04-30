<?php

namespace App\Classes\MailApi\Contract;


class MailApiClientRequest implements \JsonSerializable
{
    protected $FromAsBase64;
    protected $ToAsBase64;
    protected $SubjectAsBase64;
    protected $BodyAsBase64;
    protected $IsBodyHtml;
    protected $BccAsBase64 = null;

    public function __construct($from, $to, $subject, $body, $isBodyHtml, $isBulk = false)
    {
        $bcc = null;
        if ($isBulk) {
            $bcc = $to;
            $to = $from;
        }
        $this->FromAsBase64 = base64_encode($from);
        $this->ToAsBase64 = base64_encode($to);
        $this->SubjectAsBase64 = base64_encode($subject);
        $this->BodyAsBase64 = base64_encode($body);
        $this->IsBodyHtml = $isBodyHtml;
        $this->BccAsBase64 = $bcc ? base64_encode($bcc) : null;
    }

    public function jsonSerialize() : array
    {
        return [
            'FromAsBase64' => $this->FromAsBase64,
            'ToAsBase64' => $this->ToAsBase64,
            'SubjectAsBase64' => $this->SubjectAsBase64,
            'BodyAsBase64' => $this->BodyAsBase64,
            'IsBodyHtml' => $this->IsBodyHtml,
            'BccAsBase64' => $this->BccAsBase64,
        ];
    }
}
