git submodule add -f https://github.com/cdigruttola/optimizationseo modules/optimizationseo
git submodule add -f https://github.com/cdigruttola/cdigruttolafaq modules/cdigruttolafaq
git submodule add -f https://github.com/cdigruttola/paypaltracking modules/paypaltracking
git submodule add -f https://github.com/cdigruttola/bocustomize modules/bocustomize
git submodule add -f https://github.com/cdigruttola/packageweight modules/packageweight
git submodule add -f https://github.com/cdigruttola/prestascansecurity modules/prestascansecurity
git submodule add -f https://github.com/cdigruttola/sendcloudapi modules/sendcloudapi
git submodule add -f https://github.com/cdigruttola/electronicinvoicefields modules/electronicinvoicefields
git submodule add -f https://github.com/cdigruttola/legalblink modules/legalblink
git submodule add -f https://github.com/cdigruttola/gshoppingflux gshoppingflux && cd modules && ln -s ../gshoppingflux/gshoppingflux gshoppingflux && cd ..

git submodule add -f https://github.com/cdigruttola/falcon themes/falcon
git submodule add -f https://github.com/cdigruttola/is_productslider modules/is_productslider
git submodule add -f https://github.com/cdigruttola/is_imageslider modules/is_imageslider
git submodule add -f https://github.com/cdigruttola/is_searchbar modules/is_searchbar
git submodule add -f https://github.com/cdigruttola/is_shoppingcart modules/is_shoppingcart
git submodule add -f https://github.com/cdigruttola/is_themecore modules/is_themecore
git submodule add -f https://github.com/cdigruttola/is_favoriteproducts modules/is_favoriteproducts
git submodule add -f https://github.com/cdigruttola/is_productextratabs modules/is_productextratabs
git submodule add -f https://github.com/cdigruttola/cartrulequantity modules/cartrulequantity

cd modules/is_favoriteproducts && composer dump-autoload -o && cd ../..
cd modules/is_productslider && composer dump-autoload -o && cd ../..
cd modules/is_themecore && composer install && composer dump-autoload -o && cd ../..
cd modules/is_imageslider && composer dump-autoload -o && cd ../..
cd modules/is_searchbar && composer dump-autoload -o && cd ../..
cd modules/is_shoppingcart && composer dump-autoload -o && cd ../..
cd modules/is_productextratabs && composer dump-autoload -o && cd ../..

cd themes/falcon/_dev
npm install
cp .env-example .env
npm run build
cd ../../..
