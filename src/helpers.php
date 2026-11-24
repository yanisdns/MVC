<?php
// Petites fonctions utilisees dans les vues et les controleurs

use App\Core\Csrf;
use App\Core\Session;

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function base_path(): string
{
    $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    return rtrim($dir, '/');
}

function url(string $path = ''): string
{
    return base_path() . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(Csrf::token()) . '">';
}

function old(string $key, $default = ''): string
{
    return e($_POST[$key] ?? $default);
}

function current_user(): ?array
{
    return Session::user();
}

function date_fr(?string $date): string
{
    if (!$date) {
        return '';
    }
    $mois = [1 => 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
    $t = strtotime($date);
    return date('j', $t) . ' ' . $mois[(int) date('n', $t)] . ' ' . date('Y', $t);
}

function excerpt(string $text, int $length = 170): string
{
    $text = trim(preg_replace('/\s+/', ' ', strip_tags($text)));
    $text = preg_replace('/^#+\s*/', '', $text);
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    $cut = mb_substr($text, 0, $length);
    $space = mb_strrpos($cut, ' ');
    if ($space !== false) {
        $cut = mb_substr($cut, 0, $space);
    }
    return $cut . '...';
}

function slugify(string $text): string
{
    $text = mb_strtolower(trim($text), 'UTF-8');
    $map = [
        'à' => 'a', 'â' => 'a', 'ä' => 'a', 'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
        'î' => 'i', 'ï' => 'i', 'ô' => 'o', 'ö' => 'o', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
        'ç' => 'c', 'œ' => 'oe', 'æ' => 'ae', 'ñ' => 'n',
    ];
    $text = strtr($text, $map);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

// Met en forme le contenu d'un article : paragraphes, sous-titres ("## ") et listes ("- ").
// Tout est echappe avant d'etre transforme, donc aucun HTML saisi n'est execute.
function format_content(string $text): string
{
    $text = str_replace("\r\n", "\n", trim($text));
    $blocks = preg_split("/\n{2,}/", $text);
    $html = '';
    foreach ($blocks as $block) {
        $block = trim($block);
        if ($block === '') {
            continue;
        }
        if (strncmp($block, '## ', 3) === 0) {
            $html .= '<h2>' . e(substr($block, 3)) . "</h2>\n";
        } elseif (strncmp($block, '- ', 2) === 0) {
            $html .= "<ul>\n";
            foreach (explode("\n", $block) as $line) {
                $html .= '<li>' . e(preg_replace('/^-\s*/', '', $line)) . "</li>\n";
            }
            $html .= "</ul>\n";
        } else {
            $html .= '<p>' . nl2br(e($block)) . "</p>\n";
        }
    }
    return $html;
}

function role_label(string $role): string
{
    return ['ADMIN' => 'Administrateur', 'AUTHOR' => 'Auteur', 'USER' => 'Membre'][$role] ?? $role;
}
