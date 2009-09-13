<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2008 Mike Cochrane <code@gardyneholt.co.nz>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Mike Cochrane <mikec@jojocms.org>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */


// Images
$data = Jojo::selectQuery("SELECT * FROM {page} WHERE pg_link = 'Jojo_Plugin_Jojo_Reflect'");
if (!count($data)) {
    echo "Adding <b>Image Reflection Handler</b> Page<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Image Reflection Handler', pg_link = 'Jojo_Plugin_Jojo_Reflect', pg_url = 'reflect', pg_parent= ?, pg_order=0, pg_mainnav='no', pg_footernav='no', pg_sitemapnav='no', pg_xmlsitemapnav='no', pg_index='no', pg_body = ''", $_NOT_ON_MENU_ID);
}