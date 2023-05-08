<?php

namespace App\Service\Encryptors;

/**
 * Encryptor interface for encryptors
 */
interface EncryptorInterface
{
    /**
     * Must accept secret key for encryption
     *
     * @param string $secretKey the encryption key
     * @param string $suffix
     */
    public function __construct(string $secretKey, string $suffix);

    /**
     * @param string|null $data Encrypted text
     * @return string|null Plain text
     */
    public function decrypt(?string $data): ?string;

    /**
     * @param string|null $data Plain text to encrypt
     * @return string Encrypted text
     */
    public function encrypt(?string $data): string;

    /**
     * @param string|array|null $data Data to encrypt
     * @return string
     */
    public function encryptData(string|array|null $data): string|array;

    /**
     * @return string
     */
    public function getSuffix(): string;
}
