		==Banner Block==

==Installation and Configuration==

1. Download the package
2. Copy and paste under Magento root directory, folder structure should be like this
3. magento_root/app/code/Ipragmatech/Bannerblock.
4. Go to the Magento root directory from your terminal
5. Run the command : sudo php bin/magento setup:upgrade
6. Delete the di, generation and cache from var/
7. Run the command: sudo php bin/magento setup:di:compile
8. Run the command: sudo php bin/magento cache:clean
9. Give the read and execute permission var/di, var/generation, var/cache
10. Go to store >> configuration >> Ipragmatech Extension>>Banner Block
11. Enable the extension if not.
12. Under “banner block setting” select the category which you want to display and choose yes if you want to display image.
13. Under “popular menu setting” select the menus which you want to display.
14. Select the limit of new product and feature product.
15. For slider configuration Goto>>admin>>Banner block>>Ipramateh slider
16. Add image link and group.
17. There is an option to show the image in Mobile app or not if you select no then in APIs response that image will not be responded


