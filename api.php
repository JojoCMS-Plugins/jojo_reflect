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


Jojo::registerURI("[action:reflect|reflectall]/[file:(.*)$]", 'JOJO_Plugin_Jojo_Reflect');           // "reflect/somewhere/something.jpg" for image files