<?php

namespace core\auth;

class GenerateToken
{
    public static function generateToken(): string
    {
        $token = bin2hex(random_bytes(32)); // Génère une chaîne hexadécimale aléatoire de 64 caractères


        return $token; // Combine le jeton et la date d'expiration dans une seule chaîne
    }

    public static function generateExpiry(): string
    {
        return time() + 4 * 3600;
    }

    public static function verifyToken(string $token,string $tokenExpiry ): bool
    {
        if (!ctype_xdigit($token) || strlen($token) !== 64) {
            return false;
        }

        if (time() > $tokenExpiry) {
            return false; // Jeton expiré
        }

        return true; // Jeton valide
    }

}