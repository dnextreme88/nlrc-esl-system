<?php

namespace App\Enums;

enum Proficiencies: string
{
    case ADVANCED = 'Advanced';
    case BEGINNER = 'Beginner';
    case INTERMEDIATE = 'Intermediate';
    case MASTERY = 'Mastery';
    case PRE_INTERMEDIATE = 'Pre-Intermediate';
    case UPPER_INTERMEDIATE = 'Upper-Intermediate';

    public static function levelCode(Proficiencies $label): ?string
    {
        return match ($label) {
            self::ADVANCED => 'C1',
            self::BEGINNER => 'A1',
            self::INTERMEDIATE => 'B1',
            self::MASTERY => 'C2',
            self::PRE_INTERMEDIATE => 'A2',
            self::UPPER_INTERMEDIATE => 'B2',
        };
    }

    public static function levelDescription(Proficiencies $label): ?string
    {
        return match ($label) {
            self::ADVANCED => 'Can understand a wide range of demanding, longer texts and recognize subtle differences in meaning.',
            self::BEGINNER => 'Can understand and use simple phrases and sentences related to everyday needs.',
            self::INTERMEDIATE => 'Can understand the main points of clear standard input on familiar matters.`',
            self::MASTERY => 'Can understand virtually everything heard or read with ease.',
            self::PRE_INTERMEDIATE => 'Can understand and use common phrases and sentences to cope with everyday situations.',
            self::UPPER_INTERMEDIATE => 'Can understand the main ideas of complex text on both concrete and abstract topics.',
        };
    }
}
