<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
if (!function_exists('__ttdf_is_hex')) {
    function __ttdf_is_hex($hex)
    {
        return is_string($hex) && preg_match('/^#([a-f0-9]{6}|[a-f0-9]{3})$/i', $hex);
    }
}
if (!function_exists('__ttdf_hex_to_rgb')) {
    function __ttdf_hex_to_rgb($hex)
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $r = hexdec(str_repeat($hex[0], 2));
            $g = hexdec(str_repeat($hex[1], 2));
            $b = hexdec(str_repeat($hex[2], 2));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        return [$r, $g, $b];
    }
}
if (!function_exists('__ttdf_rgb_to_hsl')) {
    function __ttdf_rgb_to_hsl($r, $g, $b)
    {
        $r /= 255;
        $g /= 255;
        $b /= 255;
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $h = $s = $l = ($max + $min) / 2;
        if ($max === $min) {
            $h = $s = 0;
        } else {
            $d = $max - $min;
            $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);
            switch ($max) {
                case $r:
                    $h = ($g - $b) / $d + ($g < $b ? 6 : 0);
                    break;
                case $g:
                    $h = ($b - $r) / $d + 2;
                    break;
                default:
                    $h = ($r - $g) / $d + 4;
            }
            $h /= 6;
        }
        return [$h, $s, $l];
    }
}
if (!function_exists('__ttdf_hsl_to_rgb')) {
    function __ttdf_hsl_to_rgb($h, $s, $l)
    {
        $r = $g = $b = $l;
        if ($s != 0) {
            $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
            $p = 2 * $l - $q;
            $r = __ttdf_hue2rgb($p, $q, $h + 1 / 3);
            $g = __ttdf_hue2rgb($p, $q, $h);
            $b = __ttdf_hue2rgb($p, $q, $h - 1 / 3);
        }
        return [round($r * 255), round($g * 255), round($b * 255)];
    }
}
if (!function_exists('__ttdf_hue2rgb')) {
    function __ttdf_hue2rgb($p, $q, $t)
    {
        if ($t < 0) $t += 1;
        if ($t > 1) $t -= 1;
        if ($t < 1 / 6) return $p + ($q - $p) * 6 * $t;
        if ($t < 1 / 2) return $q;
        if ($t < 2 / 3) return $p + ($q - $p) * (2 / 3 - $t) * 6;
        return $p;
    }
}
if (!function_exists('__ttdf_rgb_to_hex')) {
    function __ttdf_rgb_to_hex($r, $g, $b)
    {
        $r = max(0, min(255, $r));
        $g = max(0, min(255, $g));
        $b = max(0, min(255, $b));
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }
}
if (!function_exists('__ttdf_lighten')) {
    function __ttdf_lighten($hex, $pct)
    {
        [$r, $g, $b] = __ttdf_hex_to_rgb($hex);
        [$h, $s, $l] = __ttdf_rgb_to_hsl($r, $g, $b);
        $l = min(1, max(0, $l + $pct));
        [$r2, $g2, $b2] = __ttdf_hsl_to_rgb($h, $s, $l);
        return __ttdf_rgb_to_hex($r2, $g2, $b2);
    }
}
if (!function_exists('__ttdf_darken')) {
    function __ttdf_darken($hex, $pct)
    {
        return __ttdf_lighten($hex, -$pct);
    }
}
if (!function_exists('__ttdf_contrast')) {
    function __ttdf_contrast($hex)
    {
        [$r, $g, $b] = __ttdf_hex_to_rgb($hex);
        $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
        return $yiq >= 128 ? '#000000' : '#ffffff';
    }
}
if (!function_exists('__ttdf_set_l')) {
    function __ttdf_set_l($hex, $l)
    {
        [$r, $g, $b] = __ttdf_hex_to_rgb($hex);
        [$h, $s, $oldl] = __ttdf_rgb_to_hsl($r, $g, $b);
        $l = min(1, max(0, $l));
        [$r2, $g2, $b2] = __ttdf_hsl_to_rgb($h, $s, $l);
        return __ttdf_rgb_to_hex($r2, $g2, $b2);
    }
}
if (!function_exists('__ttdf_adjust_sl')) {
    function __ttdf_adjust_sl($hex, $sDelta, $lDelta)
    {
        [$r, $g, $b] = __ttdf_hex_to_rgb($hex);
        [$h, $s, $l] = __ttdf_rgb_to_hsl($r, $g, $b);
        $s = min(1, max(0, $s + $sDelta));
        $l = min(1, max(0, $l + $lDelta));
        [$r2, $g2, $b2] = __ttdf_hsl_to_rgb($h, $s, $l);
        return __ttdf_rgb_to_hex($r2, $g2, $b2);
    }
}
if (!function_exists('__ttdf_set_hsl')) {
    function __ttdf_set_hsl($hex, $h, $s, $l)
    {
        [$r, $g, $b] = __ttdf_hex_to_rgb($hex);
        [$h0, $s0, $l0] = __ttdf_rgb_to_hsl($r, $g, $b);
        $h = ($h === null) ? $h0 : $h;
        $s = ($s === null) ? $s0 : $s;
        $l = ($l === null) ? $l0 : $l;
        [$r2, $g2, $b2] = __ttdf_hsl_to_rgb($h, $s, $l);
        return __ttdf_rgb_to_hex($r2, $g2, $b2);
    }
}
if (!function_exists('__ttdf_yiq')) {
    function __ttdf_yiq($hex)
    {
        [$r, $g, $b] = __ttdf_hex_to_rgb($hex);
        return (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
    }
}
if (!function_exists('__ttdf_normalize_color')) {
    function __ttdf_normalize_color($val)
    {
        if (!is_string($val)) return '';
        $val = trim($val);
        if (__ttdf_is_hex($val)) return $val;
        if (preg_match('/^rgba?\((\d+)\s*,\s*(\d+)\s*,\s*(\d+)(?:\s*,\s*(\d*\.?\d+))?\)$/i', $val, $m)) {
            return __ttdf_rgb_to_hex((int)$m[1], (int)$m[2], (int)$m[3]);
        }
        return '';
    }
}
$__colors = [
    'primary' => __ttdf_normalize_color(Get::Options('theme_color')),
    'secondary' => __ttdf_normalize_color(Get::Options('secondary_color')),
    'accent' => __ttdf_normalize_color(Get::Options('accent_color')),
    'neutral' => __ttdf_normalize_color(Get::Options('neutral_color')),
    'info' => __ttdf_normalize_color(Get::Options('info_color')),
    'success' => __ttdf_normalize_color(Get::Options('success_color')),
    'warning' => __ttdf_normalize_color(Get::Options('warning_color')),
    'error' => __ttdf_normalize_color(Get::Options('error_color'))
];
$__intensity = Get::Options('color_intensity') ?: 'medium';
$__radius = Get::Options('border_radius') ?: '';
$__dynamic_color_css = '';
foreach ($__colors as $name => $hex) {
    if (__ttdf_is_hex($hex)) {
        $__dynamic_color_css .= "--color-$name: $hex;";
        $__dynamic_color_css .= "--color-$name-content: " . __ttdf_contrast($hex) . ";";
        $__dynamic_color_css .= "--color-$name-100: " . __ttdf_lighten($hex, 0.3) . ";";
        $__dynamic_color_css .= "--color-$name-200: " . __ttdf_lighten($hex, 0.15) . ";";
        $__dynamic_color_css .= "--color-$name-300: " . __ttdf_darken($hex, 0.1) . ";";
    }
}
$__neutral = $__colors['neutral'];
if (__ttdf_is_hex($__neutral)) {
    [$rn, $gn, $bn] = __ttdf_hex_to_rgb($__neutral);
    [$hn, $sn, $ln] = __ttdf_rgb_to_hsl($rn, $gn, $bn);
    $s100 = min(0.50, max(0.25, $sn * 0.55));
    $s200 = min(0.55, max(0.30, $sn * 0.60));
    $s300 = min(0.60, max(0.35, $sn * 0.65));
    $sFactor = 1.0;
    $lOffset = 0.0;
    if ($__intensity === 'soft') {
        $sFactor = 0.85;
        $lOffset = 0.02;
    } else if ($__intensity === 'bold') {
        $sFactor = 1.15;
        $lOffset = -0.02;
    }
    $s100 = min(1, max(0, $s100 * $sFactor));
    $s200 = min(1, max(0, $s200 * $sFactor));
    $s300 = min(1, max(0, $s300 * $sFactor));
    $base100 = __ttdf_set_hsl($__neutral, $hn, $s100, min(1, max(0, 0.95 + $lOffset)));
    $base200 = __ttdf_set_hsl($__neutral, $hn, $s200, min(1, max(0, 0.90 + $lOffset)));
    $base300 = __ttdf_set_hsl($__neutral, $hn, $s300, min(1, max(0, 0.85 + $lOffset)));
    $sC = min(0.60, max(0.30, $sn * 0.80));
    $sC = min(1, max(0, $sC * ($__intensity === 'soft' ? 0.9 : ($__intensity === 'bold' ? 1.1 : 1))));
    $lC = 0.22 + ($__intensity === 'soft' ? 0.02 : ($__intensity === 'bold' ? -0.02 : 0));
    $baseContent = __ttdf_set_hsl($__neutral, $hn, $sC, $lC);
    if (abs(__ttdf_yiq($base200) - __ttdf_yiq($baseContent)) < 120) {
        $lC = (__ttdf_yiq($base200) > __ttdf_yiq($baseContent)) ? (0.18 + ($__intensity === 'soft' ? 0.02 : ($__intensity === 'bold' ? -0.02 : 0))) : (0.26 + ($__intensity === 'soft' ? 0.02 : ($__intensity === 'bold' ? -0.02 : 0)));
        $baseContent = __ttdf_set_hsl($__neutral, $hn, $sC, $lC);
    }
    $__dynamic_color_css .= "--color-base-100: $base100;";
    $__dynamic_color_css .= "--color-base-200: $base200;";
    $__dynamic_color_css .= "--color-base-300: $base300;";
    $__dynamic_color_css .= "--color-base-content: $baseContent;";
}
$__dynamic_color_css_dark = '';
if (__ttdf_is_hex($__neutral)) {
    [$rn, $gn, $bn] = __ttdf_hex_to_rgb($__neutral);
    [$hn, $sn, $ln] = __ttdf_rgb_to_hsl($rn, $gn, $bn);
    $sd = min(0.45, max(0.25, $sn * 0.50));
    $sd = min(1, max(0, $sd * ($__intensity === 'soft' ? 0.9 : ($__intensity === 'bold' ? 1.1 : 1))));
    $base100d = __ttdf_set_hsl($__neutral, $hn, $sd, 0.16);
    $base200d = __ttdf_set_hsl($__neutral, $hn, $sd, 0.20);
    $base300d = __ttdf_set_hsl($__neutral, $hn, $sd, 0.24);
    $sCd = min(0.60, max(0.30, $sn * 0.70));
    $sCd = min(1, max(0, $sCd * ($__intensity === 'soft' ? 0.9 : ($__intensity === 'bold' ? 1.1 : 1))));
    $lCd = 0.85;
    $baseContentD = __ttdf_set_hsl($__neutral, $hn, $sCd, $lCd);
    if (abs(__ttdf_yiq($base200d) - __ttdf_yiq($baseContentD)) < 120) {
        $lCd = (__ttdf_yiq($base200d) > __ttdf_yiq($baseContentD)) ? 0.88 : 0.80;
        $baseContentD = __ttdf_set_hsl($__neutral, $hn, $sCd, $lCd);
    }
    $__dynamic_color_css_dark .= "--color-base-100: $base100d;";
    $__dynamic_color_css_dark .= "--color-base-200: $base200d;";
    $__dynamic_color_css_dark .= "--color-base-300: $base300d;";
    $__dynamic_color_css_dark .= "--color-base-content: $baseContentD;";
}
foreach ($__colors as $name => $hex) {
    if (__ttdf_is_hex($hex)) {
        $dark = __ttdf_adjust_sl($hex, 0.05, 0.10);
        $__dynamic_color_css_dark .= "--color-$name: $dark;";
        $__dynamic_color_css_dark .= "--color-$name-content: " . __ttdf_contrast($dark) . ";";
        $__dynamic_color_css_dark .= "--color-$name-100: " . __ttdf_lighten($dark, 0.20) . ";";
        $__dynamic_color_css_dark .= "--color-$name-200: " . __ttdf_lighten($dark, 0.10) . ";";
        $__dynamic_color_css_dark .= "--color-$name-300: " . __ttdf_darken($dark, 0.05) . ";";
    }
}
$__theme_meta_color = (__ttdf_is_hex($__colors['primary']) ? $__colors['primary'] : (__ttdf_is_hex($__neutral) ? __ttdf_lighten($__neutral, 0.4) : '#ffffff'));
// Radius variables
if (is_string($__radius) && $__radius !== '') {
    $__dynamic_color_css .= "--radius-selector: $__radius;--radius-field: $__radius;--radius-box: $__radius;";
    $__dynamic_color_css_dark .= "--radius-selector: $__radius;--radius-field: $__radius;--radius-box: $__radius;";
}
?>
<meta name="theme-color" content="<?php echo $__theme_meta_color; ?>" />
<style>
    :root {
        <?php echo $__dynamic_color_css; ?>
    }

    @media (prefers-color-scheme: dark) {
        :root {
            <?php echo $__dynamic_color_css_dark; ?>
        }
    }
</style>