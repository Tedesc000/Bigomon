<?php
// Imagens dos TMs (id_item = 1000 + id_movimento).
// O sprite vem da PokeAPI pelo tipo do ataque: tm-<tipo>.png

const TIPOS_TM_SLUG = [
    1 => 'normal', 2 => 'fighting', 3 => 'flying', 4 => 'poison', 5 => 'ground',
    6 => 'rock', 7 => 'bug', 8 => 'ghost', 9 => 'steel', 10 => 'fire',
    11 => 'water', 12 => 'grass', 13 => 'electric', 14 => 'psychic', 15 => 'ice',
    16 => 'dragon', 17 => 'dark',
];

// id_item do TM => id_tipo do movimento
const TM_TIPO_POR_ITEM = [
    1001 => 1, // Tackle,
    1002 => 1, // Mega Kick,
    1003 => 1, // Relic Song,
    1004 => 1, // Quick Attack,
    1069 => 1, // Hyper Voice,
    1070 => 1, // Boomburst,
    1005 => 2, // Low Kick,
    1006 => 2, // Close Combat,
    1007 => 2, // Aura Sphere,
    1008 => 2, // Mach Punch,
    1071 => 2, // Focus Blast,
    1072 => 2, // Vacuum Wave,
    1009 => 3, // Gust,
    1010 => 3, // Brave Bird,
    1011 => 3, // Air Slash,
    1012 => 3, // Wing Attack,
    1073 => 3, // Acrobatics,
    1074 => 3, // Hurricane,
    1013 => 4, // Poison Sting,
    1014 => 4, // Gunk Shot,
    1015 => 4, // Poison Jab,
    1016 => 4, // Venoshock,
    1075 => 4, // Sludge Bomb,
    1076 => 4, // Acid Spray,
    1017 => 5, // Mud-Slap,
    1018 => 5, // Earthquake,
    1019 => 5, // Earth Power,
    1020 => 5, // Dig,
    1077 => 5, // Mud Shot,
    1078 => 5, // High Horsepower,
    1021 => 6, // Rock Throw,
    1022 => 6, // Stone Edge,
    1023 => 6, // Ancient Power,
    1024 => 6, // Accelerock,
    1079 => 6, // Power Gem,
    1080 => 6, // Meteor Beam,
    1025 => 7, // Bug Bite,
    1026 => 7, // Megahorn,
    1027 => 7, // Bug Buzz,
    1028 => 7, // First Impression,
    1081 => 7, // Pollen Puff,
    1082 => 7, // Signal Beam,
    1029 => 8, // Astonish,
    1030 => 8, // Poltergeist,
    1031 => 8, // Shadow Ball,
    1032 => 8, // Shadow Sneak,
    1083 => 8, // Hex,
    1084 => 8, // Ominous Wind,
    1033 => 9, // Metal Claw,
    1034 => 9, // Heavy Slam,
    1035 => 9, // Flash Cannon,
    1036 => 9, // Bullet Punch,
    1085 => 9, // Steel Beam,
    1086 => 9, // Mirror Shot,
    1037 => 10, // Ember,
    1038 => 10, // Fire Blast,
    1039 => 10, // Flame Wheel,
    1040 => 10, // Fire Punch,
    1087 => 10, // Flamethrower,
    1088 => 10, // Flare Blitz,
    1041 => 11, // Water Gun,
    1042 => 11, // Hydro Pump,
    1043 => 11, // Waterfall,
    1044 => 11, // Aqua Jet,
    1089 => 11, // Scald,
    1090 => 11, // Liquidation,
    1045 => 12, // Vine Whip,
    1046 => 12, // Solar Beam,
    1047 => 12, // Giga Drain,
    1048 => 12, // Razor Leaf,
    1091 => 12, // Energy Ball,
    1092 => 12, // Leaf Blade,
    1049 => 13, // Thunder Shock,
    1050 => 13, // Thunder,
    1051 => 13, // Thunderbolt,
    1052 => 13, // Spark,
    1093 => 13, // Thunder Punch,
    1094 => 13, // Volt Tackle,
    1053 => 14, // Confusion,
    1054 => 14, // Psychic,
    1055 => 14, // Psybeam,
    1056 => 14, // Zen Headbutt,
    1095 => 14, // Psycho Cut,
    1096 => 14, // Psychic Fangs,
    1057 => 15, // Ice Shard,
    1058 => 15, // Blizzard,
    1059 => 15, // Ice Beam,
    1060 => 15, // Icicle Crash,
    1097 => 15, // Freeze-Dry,
    1098 => 15, // Ice Spinner,
    1061 => 16, // Dragon Claw,
    1062 => 16, // Outrage,
    1063 => 16, // Dragon Breath,
    1064 => 16, // Dragon Rush,
    1099 => 16, // Draco Meteor,
    1100 => 16, // Dragon Pulse,
    1065 => 17, // Bite,
    1066 => 17, // Knock Off,
    1067 => 17, // Dark Pulse,
    1068 => 17, // Sucker Punch,
    1101 => 17, // Snarl,
    1102 => 17, // Night Daze
];

function urlImagemTM(int $idItem): ?string {
    $idTipo = TM_TIPO_POR_ITEM[$idItem] ?? null;
    if ($idTipo === null || !isset(TIPOS_TM_SLUG[$idTipo])) {
        return null;
    }
    return 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/tm-' . TIPOS_TM_SLUG[$idTipo] . '.png';
}

// URL do sprite de qualquer item: TMs pelo tipo, mapa especial por id e o resto pelo nome (slug da PokeAPI)
function urlImagemItem(int $idItem, string $nome): string {
    $mapaPorId = [
        814 => 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/key-stone.png',
    ];
    if (isset($mapaPorId[$idItem])) {
        return $mapaPorId[$idItem];
    }
    if ($urlTM = urlImagemTM($idItem)) {
        return $urlTM;
    }
    $slug = strtolower(trim($nome));
    $slug = str_replace(['é', 'è', 'ê'], 'e', $slug);
    $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
    $slug = trim($slug, '-');
    return "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/{$slug}.png";
}
