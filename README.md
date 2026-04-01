# iCore · Filament 管理后台脚手架

一个面向中文业务场景的 Laravel + Filament 脚手架，用于快速搭建后台管理系统与 API 服务。

内置用户/角色权限、系统设置、钱包与支付能力，并集成 API 文档生成。

![License](https://img.shields.io/badge/License-MIT-blue?style=flat-square)
![PHP Version](https://img.shields.io/badge/PHP-8.5-blue?style=flat-square&logo=php)
![Laravel Version](https://img.shields.io/badge/Laravel-13.0-red?style=flat-square&logo=laravel)
![Filament Version](https://img.shields.io/badge/Filament-5.0-purple?style=flat-square)

## 版本信息

- 当前版本：`v5.0.0`
- 基础技术栈：Laravel 13 / Filament 5 / Livewire 4

参考项目：

- https://gitee.com/xujinhui/filament
- https://filamentphp.com/plugins/siubie-kaido-kit
- https://filamentphp.com/plugins/riodewanto-superduper-starter

## 功能特性

### 开发体验

- 基于 Filament 快速生成 CRUD 与管理页面
- 内置自定义代码模板（`stubs/*.stub`）
- 集成 Scramble 自动生成 API 文档（`/docs/api`）

### 认证与授权

- 基于 Filament Shield 的 RBAC 权限体系
- 支持双因素认证
- 支持超级管理员快捷配置

### 业务能力

- 钱包管理与资金流水
- 微信/支付宝支付集成示例
- 系统动态设置管理

## 快速开始（本地环境）

1. 克隆项目并进入目录：

```bash
git clone git@github.com:imnpc/iCore.git
cd iCore
```

2. 安装依赖：

```bash
composer install
npm install
```

3. 初始化环境变量：

```bash
cp .env.example .env
```

4. 配置数据库（编辑 `.env`）：

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=demo
DB_USERNAME=root
DB_PASSWORD=
```

5. 可选：配置 Resend 邮件：

```bash
MAIL_MAILER=resend
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
RESEND_API_KEY=
MAIL_FROM_ADDRESS="admin@domain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

6. 执行初始化命令：

```bash
php artisan key:generate
php artisan migrate
php artisan storage:link
```

7. 初始化后台权限与账号（如已导入项目 SQL 可按需跳过）：

```bash
php artisan make:filament-user
php artisan shield:generate --all --ignore-existing-policies --panel=admin
php artisan shield:install admin
php artisan shield:super-admin --user=1 --panel=admin
php artisan filament:optimize
```

8. 构建前端资源并访问系统：

```bash
npm run build
```

- 管理后台：`/admin`
- API 文档：`/docs/api`

## 使用 Sail（Docker）

1. 克隆项目并进入目录：

```bash
git clone git@github.com:imnpc/iCore.git
cd iCore
```

2. 初始化环境与依赖：

```bash
cp .env.example .env
composer install
```

3. 安装并启动 Sail：

```bash
php artisan sail:install --no-interaction
./vendor/bin/sail up -d
```

4. 执行初始化命令：

```bash
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan make:filament-user
./vendor/bin/sail artisan shield:generate --all --ignore-existing-policies --panel=admin
./vendor/bin/sail artisan shield:install admin
./vendor/bin/sail artisan shield:super-admin --user=1 --panel=admin
./vendor/bin/sail artisan filament:optimize
./vendor/bin/sail npm run build
```

访问 `/admin` 登录后台。

## 常用命令

### 缓存与构建

```bash
php artisan cache:clear
php artisan optimize:clear
composer dump-autoload
php artisan filament:optimize
php artisan icons:cache
```

### AI 辅助（Laravel Boost）

```bash
composer require laravel/boost --dev
php artisan boost:install
php artisan boost:update --discover
```

### 常见生成命令

```bash
# 订单相关
php artisan make:model Order -m
php artisan make:controller Api/OrderController --model=App\\Models\\Order
php artisan make:request Api/OrderRequest
php artisan make:resource OrderResource
php artisan make:policy OrderPolicy
php artisan make:job OrderPaid
php artisan make:filament-resource Order --generate

# 按现有策略增量生成权限
php artisan shield:generate --all --ignore-existing-policies --panel=admin

# 关联管理与枚举
php artisan make:filament-relation-manager UserResource userWalletLog
php artisan make:enum PayType
```

## 插件与组件

### Filament 生态

- bezhansalleh/filament-shield（权限管理）
- bezhansalleh/filament-language-switch（语言切换）
- maggomann/filament-model-translator（模型翻译）
- outerweb/filament-settings（系统设置）
- pxlrbt/filament-excel（Excel 导出）
- devonab/filament-easy-footer（底部版权）
- filament/spatie-laravel-tags-plugin（标签能力）
- imnpc/filament-wallet（钱包后台）
- imnpc/filament-bandel（用户封禁）

### 支付与财务

- overtrue/laravel-wechat（微信 SDK）
- yansongda/laravel-pay（支付宝/微信支付）
- bavix/laravel-wallet（虚拟钱包）

### 开发与基础能力

- dedoc/scramble（API 文档）
- spatie/laravel-route-attributes（路由注解）
- spatie/laravel-query-builder（查询构建）
- resend/resend-laravel（邮件服务）

## 截图

![后台截图](screenshot.png)
