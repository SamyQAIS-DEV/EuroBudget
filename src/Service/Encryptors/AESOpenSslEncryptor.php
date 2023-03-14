<?php

namespace App\Service\Encryptors;

use App\Exception\EncryptionException;
use LogicException;

class AESOpenSslEncryptor implements EncryptorInterface
{
    const METHOD = 'aes-256-cbc';

    private bool $enableEncryption = true;

    private string $secretKey;

    private string $suffix;

    public function __construct(string $secretKey, string $suffix)
    {
        $this->secretKey = md5($secretKey);
        $this->suffix = $suffix;
    }

    /**
     * @param string|null $dataBase64
     * @return string
     */
    public function decrypt(?string $dataBase64): string
    {
        if ($this->enableEncryption === false) {
            return null !== $dataBase64 ? $dataBase64 : "";
        }

        if (is_null($dataBase64) || !$this->secretKey) {
            return "";
        }
        $dataBase64 = strtr($dataBase64, $this->suffix, '');
        $data = base64_decode($dataBase64);
        $initVectorSize = openssl_cipher_iv_length(self::METHOD);
        if (!$initVectorSize) {
            return "";
        }
        $initVector = substr($this->secretKey, $initVectorSize);
        $dataAndMetadataStr = openssl_decrypt($data, self::METHOD, $this->secretKey, OPENSSL_RAW_DATA, $initVector);
        if (null == $dataAndMetadataStr) {
            throw new LogicException('decrypt operation failed $initVector: "' . $initVector . '", $data : "' . $data . '", $dataBase64: "' . $dataBase64 . '"');
        }
        /** @var array<string, string> $dataAndMetadata */
        $dataAndMetadata = json_decode($dataAndMetadataStr, true);
        if (null == $dataAndMetadata) {
            throw new LogicException('decrypt operation failed, the decrypted structure of "' . $data . '" is not properly formed! (json expected, null obtained)');
        }
        $length = strlen($dataAndMetadata['data']);
        if (intval($dataAndMetadata['length']) !== $length) {
            throw new LogicException('Integrity check failed on crypted data, should be "' . $dataAndMetadata['length'] . '" chars length, "' . $length . '" chars obtained!');
        }
        return $dataAndMetadata['data'];
    }

    /**
     * {@inheritdoc}
     */
    public function encrypt(?string $data): string
    {
        if ($this->enableEncryption === false) {
            return null !== $data ? $data : "";
        }
        if (is_null($data) || !$this->secretKey) {
            throw new EncryptionException();
        }
        $initVectorSize = openssl_cipher_iv_length(self::METHOD);
        if (!$initVectorSize) {
            throw new EncryptionException();
        }
        $initVector = substr($this->secretKey, $initVectorSize);
        $length = strlen($data);
        $dataAndMetadata = json_encode(['data' => $data, 'length' => $length]);
        if (!$dataAndMetadata) {
            throw new EncryptionException();
        }
        $ciphertext = openssl_encrypt($dataAndMetadata, self::METHOD, $this->secretKey, OPENSSL_RAW_DATA, $initVector);
        if (!$ciphertext) {
            throw new EncryptionException();
        }
        return base64_encode($ciphertext) . $this->suffix;
    }

    public function getSuffix(): string
    {
        if (!empty($this->suffix)) {
            return $this->suffix;
        }
        return '';
    }

    /**
     * @param bool $enableEncryption
     * @return AESOpenSslEncryptor
     */
    public function setEnableEncryption(bool $enableEncryption): self
    {
        $this->enableEncryption = $enableEncryption;

        return $this;
    }
}
