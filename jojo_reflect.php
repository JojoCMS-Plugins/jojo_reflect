<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2008 Michael Cochrane <mikec@jojocms.org>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Michael Cochrane <mikec@jojocms.org>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */

class Jojo_Plugin_Jojo_Reflect extends Jojo_Plugin {

    /**
     * Output the Image file
     *
     */
    function __construct()
    {
        $action = Jojo::getFormData('action', 'reflect');

        /* Get requested filename */
        $file = Jojo::getFormData('file', 'default.jpg');
        $timestamp = strtotime('+1 day');

        /* Check file name has correct extension */
        $validExtensions = array('jpg', 'gif', 'jpeg', 'png');
        if (!in_array(Jojo::getFileExtension(basename($file)), $validExtensions)) {
            /* Not valid, 404 */
            header("HTTP/1.0 404 Not Found", true, 404);
            exit;
        }

        /* Check for existance of cached copy if user has not pressed CTRL-F5 */
        $cachefile = _CACHEDIR . '/' . $action . '/' . $file;
        if (is_file($cachefile) && !Jojo::ctrlF5()) {
            Jojo_Plugin_Jojo_Reflect::sendCacheHeaders(filemtime($cachefile));

            /* output image data */
            $data = file_get_contents($cachefile);
            header('Cache-Control: private');
            header('Last-Modified: ' . date('D, d M Y H:i:s \G\M\T', filemtime($cachefile)));
            header('Content-type: image/png');
            header('Content-Length: ' . strlen($data));
            header('Content-Disposition: inline; filename=' . basename($file) . ';');
            header('Content-Description: PHP Generated Image (cached)');
            header('Content-Transfer-Encoding: binary');
            echo $data;
            exit();
        }

        $url = _SITEURL . '/images/' . $file;
        $imgStr = file_get_contents($url);
        if (!$imgStr) {
            /* Not found, 404 */
            header("HTTP/1.0 404 Not Found", true, 404);
            exit;
        }

        $source = imagecreatefromstring($imgStr);
        unset($imgStr);
        if (!$source) {
            /* Couldn't create image, 404 */
            header("HTTP/1.0 404 Not Found", true, 404);
            exit;
        }

        // Following code is based on the code:
        // Easy Reflections v3 by Richard Davey, Core PHP (rich@corephp.co.uk)
        // See the extenal folder for a copy of this

        //    tint (the colour used for the tint, defaults to white if not given)
        $red = 127;
        $green = 127;
        $blue = 127;

        // height (how tall should the reflection be?)
        // default to 50% of the source images height
        $output_height = 0.50;

        $alpha_start = 80;

        $alpha_end = 0;

        /*
            ----------------------------------------------------------------
            Ok, let's do it ...
            ----------------------------------------------------------------
        */
        $width = imagesx($source);
        $height = imagesy($source);

        //	Calculate the height of the output image
        if ($output_height < 1)
        {
            //	The output height is a percentage
            $new_height = $height * $output_height;
        }
        else
        {
            //	The output height is a fixed pixel value
            $new_height = $output_height;
        }

        /*
            ----------------------------------------------------------------
            Build the reflection image
            ----------------------------------------------------------------
        */

        //	We'll store the final reflection in $output. $buffer is for internal use.
        $output = imagecreatetruecolor($width, $new_height);
        $buffer = imagecreatetruecolor($width, $new_height);

        //  Save any alpha data that might have existed in the source image and disable blending
        imagesavealpha($source, true);

        imagesavealpha($output, true);
        imagealphablending($output, false);

        imagesavealpha($buffer, true);
        imagealphablending($buffer, false);

        //	Copy the bottom-most part of the source image into the output
        imagecopy($output, $source, 0, 0, 0, $height - $new_height, $width, $new_height);

        //	Rotate and flip it (strip flip method)
        for ($y = 0; $y < $new_height; $y++)
        {
           imagecopy($buffer, $output, 0, $y, 0, $new_height - $y - 1, $width, 1);
        }

        $output = $buffer;

        /*
            ----------------------------------------------------------------
            Apply the fade effect
            ----------------------------------------------------------------
        */

        //	This is quite simple really. There are 127 available levels of alpha, so we just
        //	step-through the reflected image, drawing a box over the top, with a set alpha level.
        //	The end result? A cool fade.

        //	There are a maximum of 127 alpha fade steps we can use, so work out the alpha step rate

        $alpha_length = abs($alpha_start - $alpha_end);

        imagelayereffect($output, IMG_EFFECT_OVERLAY);

        for ($y = 0; $y <= $new_height; $y++)
        {
            //  Get % of reflection height
            $pct = $y / $new_height;

            //  Get % of alpha
            if ($alpha_start > $alpha_end)
            {
                $alpha = (int) ($alpha_start - ($pct * $alpha_length));
            }
            else
            {
                $alpha = (int) ($alpha_start + ($pct * $alpha_length));
            }

            //  Rejig it because of the way in which the image effect overlay works
            $final_alpha = 127 - $alpha;

            //imagefilledrectangle($output, 0, $y, $width, $y, imagecolorallocatealpha($output, 127, 127, 127, $final_alpha));
            imagefilledrectangle($output, 0, $y, $width, $y, imagecolorallocatealpha($output, $red, $green, $blue, $final_alpha));
        }

        /*
            ----------------------------------------------------------------
            Output our final PNG
            ----------------------------------------------------------------
        */

        /* Just the reflection, or the whole thing? */
        if ($action == 'reflectall') {
            /* Whole thing, add the original image to the top */
            $reflection = $output;
            $output = imagecreatetruecolor($width, $height + $new_height + 1);
            imagesavealpha($output, true);
            imagealphablending($output, false);
            imagecopy($output, $source, 0, 0, 0, 0, $width, $height);
            imagefilledrectangle($output, 0, $height, $width, $height, imagecolorallocatealpha($output, 255, 255, 255, 127));
            imagecopy($output, $reflection, 0, $height + 1, 0, 0, $width, $new_height);
        }

        //	PNG
        header("Content-type: image/png");
        imagepng($output);

        // Save cache file
        Jojo::recursiveMkdir(dirname($cachefile));
        imagepng($output, $cachefile);
        imagedestroy($output);
        exit();
    }

    private static function sendCacheHeaders($timestamp) {
        // A PHP implementation of conditional get, see
        //   http://fishbowl.pastiche.org/archives/001132.html
        $last_modified = substr(date('r', $timestamp), 0, -5) . 'GMT';
        $etag = '"'.md5($last_modified) . '"';
        // Send the headers
        header("Last-Modified: $last_modified");
        header("ETag: $etag");
        // See if the client has provided the required headers
        $if_modified_since = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ?
            stripslashes($_SERVER['HTTP_IF_MODIFIED_SINCE']) :
            false;
        $if_none_match = isset($_SERVER['HTTP_IF_NONE_MATCH']) ?
            stripslashes($_SERVER['HTTP_IF_NONE_MATCH']) :
            false;
        if (!$if_modified_since && !$if_none_match) {
            return;
        }
        // At least one of the headers is there - check them
        if ($if_none_match && $if_none_match != $etag) {
            return; // etag is there but doesn't match
        }
        if ($if_modified_since && $if_modified_since != $last_modified) {
            return; // if-modified-since is there but doesn't match
        }
        // Nothing has changed since their last request - serve a 304 and exit
        header('HTTP/1.0 304 Not Modified');
        exit;
    }
}