#!/bin/bash

echo "dockerイメージの削除...開始"
docker rmi `docker images -q`
echo "dockerイメージの削除...完了"

echo "EBSの容量拡張(10GB->12GB)...開始"
# 変更前のディスクサイズを表示
echo "変更前のディスクサイズ=========================\n"
df -h
echo "===============================================\n"

SIZE=12

# Cluod9が紐づいているEC2インスタンスのインスタンスIDを取得
INSTANCEID=$(curl http://169.254.169.254/latest/meta-data/instance-id)

# EC2インスタンスに関連づけられているEBSのボリュームIDを取得
VOLUMEID=$(aws ec2 describe-instances \
  --instance-id $INSTANCEID \
  --query "Reservations[0].Instances[0].BlockDeviceMappings[0].Ebs.VolumeId" \
  --output text)

# EBSのボリュームサイズを変更
aws ec2 modify-volume --volume-id $VOLUMEID --size $SIZE

# サイズ変更が完了するまで、待ち
while [ \
  "$(aws ec2 describe-volumes-modifications \
    --volume-id $VOLUMEID \
    --filters Name=modification-state,Values="optimizing","completed" \
    --query "length(VolumesModifications)"\
    --output text)" != "1" ]; do
sleep 1
done

# ファイルシステムの確認
if [ $(readlink -f /dev/xvda) = "/dev/xvda" ]
then
  # パーティションテーブルを書き直し
  sudo growpart /dev/xvda 1

  # ファイルシステムのサイズを拡張
  # AL2の使用有無チェック
  STR=$(cat /etc/os-release)
  SUB="VERSION_ID=\"2\""
  if [[ "$STR" == *"$SUB"* ]]
  then
    sudo xfs_growfs -d /
  else
    sudo resize2fs /dev/xvda1
  fi

else
  # パーティションテーブルを書き直し
  sudo growpart /dev/nvme0n1 1

  # ファイルシステムのサイズを拡張
  # AL2の使用有無チェック
  STR=$(cat /etc/os-release)
  SUB="VERSION_ID=\"2\""
  if [[ "$STR" == *"$SUB"* ]]
  then
    sudo xfs_growfs -d /
  else
    sudo resize2fs /dev/nvme0n1p1
  fi
fi

# 変更後のディスクサイズを表示
echo "変更後のディスクサイズ=========================\n"
df -h
echo "===============================================\n"
echo "EBSの容量拡張...完了"

echo "OSが確保するキャッシュメモリを解放する...開始"
sudo sh -c "echo 3 > /proc/sys/vm/drop_caches"
echo "OSが確保するキャッシュメモリを解放する...完了"

echo "swap領域の拡張...開始"
sudo dd if=/dev/zero of=/var/swap.1 bs=1M count=1024
sudo chmod 600 /var/swap.1
sudo mkswap /var/swap.1
sudo swapon /var/swap.1
sudo cp -p /etc/fstab /etc/fstab.ORG
sudo sh -c "echo '/var/swap.1 swap swap defaults 0 0' >> /etc/fstab"
echo "swap領域の拡張...完了"