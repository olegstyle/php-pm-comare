port=8001
count=10

[ -z "$1" ] && echo "Default port set: ${port}" || port=$1 && echo "Port set: ${port}"
[ -z "$2" ] && echo "Default count set: ${count}" || count=$2 && echo "Count set: ${count}"

cp template.yaml load.yaml

sed -i '' "s/{PORT}/${port}/g" load.yaml
sed -i '' "s/{COUNT}/${count}/g" load.yaml

docker run \
    --rm \
    -v $(pwd):/var/loadtest \
    -v $SSH_AUTH_SOCK:/ssh-agent -e SSH_AUTH_SOCK=/ssh-agent \
    --net host \
    -it direvius/yandex-tank
