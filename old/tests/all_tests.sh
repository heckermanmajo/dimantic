php controller/rating_controller/test_rating_controller.php
# parallel --jobs 4 --no
virtue_folder=controller/post_virtue
#tests=()
for file in "$virtue_folder"/*.php ; do
    php "$file"
done

#parallel --jobs 4 --no-notice ::: "${tests[@]}"
