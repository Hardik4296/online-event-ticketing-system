<?php

namespace App\Traits;

use Cache;
use DB;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

trait Common
{
    /**
     * Generate a random ID based on the user ID and current timestamp.
     *
     * @param string $userId
     * @param bool $includeRandomInt
     * @return string
     */
    private function generateUniqueId(string $userId, bool $includeRandomInt = true): string
    {
        $timestamp = time();
        $randomInt = $includeRandomInt ? random_int(1000, 9999) : '';
        return $userId . $timestamp . $randomInt;
    }

    /**
     * Generate a group ID based on the user ID and current timestamp.
     *
     * @param string $userId
     * @return string
     */
    public function generateGroupId(string $userId): string
    {
        return $this->generateUniqueId($userId, false);
    }

    /**
     * Generate a random ID based on the user ID.
     *
     * @param string $userId
     * @return string
     */
    public function generateRandomId(string $userId): string
    {
        return $this->generateUniqueId($userId);
    }

    /**
     * Retrieve the payment intent ID from a string.
     *
     * @param string $string
     * @return string
     */
    public function retrievePaymentIntentId(string $string): string
    {
        return explode('_secret', $string, 2)[0] ?? '';
    }

    /**
     * Generate a unique cache key for event filtering.
     *
     * @param Request $request
     * @return string
     */
    private function generateEventCacheKey(Request $request): string
    {
        return implode('_', [
            'event_list_',
            $request->get('city_id', 'all'),
            $request->get('date', 'all'),
            $request->get('keyword', 'none'),
        ]);
    }

    /**
     * Clear all event list cache entries.
     *
     * @return void
     */
    public function clearEventListCache(): void
    {
        $keys = DB::table('cache')
            ->where('key', 'like', 'event_list_%')
            ->pluck('key');

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Clear the organizer's event cache.
     *
     * @return void
     */
    public function clearOrganizerEventCache($organizerId): void
    {
        Cache::forget('organizer_events_' . $organizerId);
    }

    /**
     * Encrypt the given data.
     *
     * @param string $data
     * @return string
     */
    public function encryptData(string $data): string
    {
        $secretKey = config('app.key');
        $cipher = 'aes-256-cbc';
        $iv = substr(hash('sha256', 'fixed_iv'), 0, 16);
        $key = hash('sha256', $secretKey, true);

        return base64_encode(openssl_encrypt($data, $cipher, $key, 0, $iv));
    }

    /**
     * Decrypt the given encrypted Data.
     *
     * @param string $encrypted
     * @return string|null
     */
    public function decryptData(string $encrypted): ?string
    {
        try {
            $secretKey = config('app.key');
            $cipher = 'aes-256-cbc';
            $iv = substr(hash('sha256', 'fixed_iv'), 0, 16);
            $key = hash('sha256', $secretKey, true);

            return openssl_decrypt(base64_decode($encrypted), $cipher, $key, 0, $iv) ?: null;
        } catch (\Exception $e) {

            throw new DecryptException('Decryption failed: ' . $e->getMessage());
        }
    }

    /**
     * Decrypt the given encrypted ID.
     *
     * @param string $encrypted
     * @return string|null
     */
    public function decryptId(string $encrypted): ?string
    {
        try {

            return decrypt($encrypted);
        } catch (\Exception $e) {

            throw new DecryptException('Decryption failed: ' . $e->getMessage());
        }
    }
}
