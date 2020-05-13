port=8001
rps=10

[ -z "$1" ] && echo "Default port set: ${port}" || port=$1 && echo "Port set: ${port}"
[ -z "$2" ] && echo "Default RPS set: ${rps}" || rps=$2 && echo "RPS set: ${rps}"

cp template.yaml load.yaml

sed -i '' "s/{PORT}/${port}/g" load.yaml
sed -i '' "s/{COUNT}/${rps}/g" load.yaml

docker run \
    --rm \
    -v $(pwd):/var/loadtest \
    -v $(pwd)/.ssh:/root/.ssh \
    --net host \
    -it direvius/yandex-tank
