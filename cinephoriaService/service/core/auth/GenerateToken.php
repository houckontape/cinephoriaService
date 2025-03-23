<?php

namespace core\auth;

class GenerateToken
{
    public static function generateToken(): string
    {
        $token = bin2hex(random_bytes(32)); // G�n�re une cha�ne hexad�cimale al�atoire de 64 caract�res


        return $token; // Combine le jeton et la date d'expiration dans une seule cha�ne
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
            return false; // Jeton expir�
        }

        return true; // Jeton valide
    }

}