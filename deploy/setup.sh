#!/usr/bin/env bash
# Lightsail (Amazon Linux 2023) 初期化スクリプト
# Docker / docker compose plugin / 2GB swap を用意する。
# 使い方: インスタンスに転送して `bash setup.sh`
set -euo pipefail

echo "==> Docker / git インストール"
sudo dnf -y install docker git
sudo systemctl enable --now docker
sudo usermod -aG docker ec2-user || true

echo "==> docker compose plugin"
DCP_DIR=/usr/local/lib/docker/cli-plugins
sudo mkdir -p "$DCP_DIR"
ARCH=$(uname -m)
sudo curl -fsSL "https://github.com/docker/compose/releases/latest/download/docker-compose-linux-${ARCH}" \
  -o "$DCP_DIR/docker-compose"
sudo chmod +x "$DCP_DIR/docker-compose"

echo "==> swap 2GB（MySQL 8.4 の OOM 回避）"
if [ ! -f /swapfile ]; then
  sudo dd if=/dev/zero of=/swapfile bs=1M count=2048 status=progress
  sudo chmod 600 /swapfile
  sudo mkswap /swapfile
  sudo swapon /swapfile
  echo '/swapfile none swap sw 0 0' | sudo tee -a /etc/fstab
fi

echo "==> 完了。'docker compose version' で確認（グループ反映のため再ログイン推奨）"
