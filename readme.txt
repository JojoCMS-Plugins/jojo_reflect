Image reflection Plugin
-----------------------

Creates a mirror or reflection effect on images.

To Use:
    There are two ways that you can use this plugin. The plugin output a png
    with the mirror effect. It can output just the mirror part, or the full
    image with the mirror effect at the bottom.

    Both have advantages and tradeoffs. Single image (reflectall) as the
    advantage of only one img tag and one http request but if the original is a
    photo for other large jpg the resulting png will be large. Two images
    (reflect) requires the original image and the mirror image in the html but
    may be smaller overall.

    Change any image urls from:
        images/some/image/file.jpg
    To:
        reflect/some/image/file.jpg     - just the reflection
        reflectall/some/image/file.jpg  - for the original image and the reflection