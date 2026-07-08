# 班级管理系统 (Class Management System)

基于 **Laravel 11 + Vue 3** 的全栈班级管理平台，支持班级、学生、教师的增删改查，以及基于角色的权限控制 (RBAC)。

## 技术栈

| 层级 | 技术 |
|------|------|
| 后端框架 | Laravel 11 (PHP 8.2) |
| 认证 | Laravel Sanctum (Token-based) |
| 数据库 | MySQL 8.0 |
| 前端框架 | Vue 3 (Composition API) |
| 构建工具 | Vite 6 |
| UI 组件库 | Element Plus |
| 状态管理 | Pinia |
| 路由 | Vue Router 4 |
| HTTP 客户端 | Axios |

## 功能模块

- **控制台** — 首页统计面板，展示班级、学生、教师、账号总数
- **班级管理** — 班级的增删改查
- **学生管理** — 学生的增删改查，支持班级调转
- **教师管理** — 教师的增删改查
- **账号管理** — 系统用户的创建与管理（需 `user.manage` 权限）
- **权限管理** — 角色与权限的分配（需 `role.manage` 权限）

### 权限系统

系统内置 RBAC 权限模型，通过角色 (Role) → 权限 (Permission) 的关联控制操作：

| 权限码 | 说明 |
|--------|------|
| `class.manage` | 班级增删改 |
| `student.manage` | 学生增删改及调班 |
| `teacher.manage` | 教师增删改 |
| `user.manage` | 用户/账号管理 |
| `role.manage` | 角色与权限分配 |

管理员 (`admin` 角色) 拥有所有权限，无需逐一分配。

## 项目结构

```
student system2/
├── backend-laravel/          # Laravel 后端
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/  # 控制器 (Auth, Class, Student, Teacher, User, Role, Dashboard)
│   │   │   └── Middleware/   # 自定义中间件 (CheckPermission)
│   │   ├── Models/           # 数据模型 (User, Student, Teacher, Classes, Role, Permission)
│   │   └── Providers/        # 服务提供者
│   ├── config/               # 配置文件
│   ├── database/
│   │   └── migrations/       # 数据库迁移
│   ├── routes/
│   │   └── api.php           # API 路由定义
│   └── .env                  # 环境配置
├── frontend/                 # Vue 3 前端
│   └── src/
│       ├── api/              # API 请求封装
│       ├── layouts/          # 布局组件 (AppLayout)
│       ├── router/           # 路由配置
│       ├── stores/           # Pinia 状态管理
│       ├── utils/            # 工具函数 (Axios 实例)
│       └── views/            # 页面视图
│           ├── auth/         # 登录页
│           ├── dashboard/    # 控制台
│           ├── classes/      # 班级管理
│           ├── students/     # 学生管理
│           ├── teachers/     # 教师管理
│           ├── users/        # 账号管理
│           └── roles/        # 权限管理
└── docs/                     # 文档
```

## 快速开始

### 环境要求

- PHP >= 8.2（需启用 `pdo_mysql` 扩展）
- MySQL 8.0
- Node.js >= 18
- Composer

### 1. 配置数据库

创建 MySQL 数据库：

```sql
CREATE DATABASE class_manager DEFAULT CHARACTER SET utf8mb4;
```

编辑 `backend-laravel/.env`，填入数据库连接信息：

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=class_manager
DB_USERNAME=root
DB_PASSWORD=你的密码
```

也可使用 Docker 快速启动 MySQL：

```bash
docker run --name class-manager-mysql \
  -e MYSQL_ROOT_PASSWORD=root \
  -e MYSQL_DATABASE=class_manager \
  -p 3306:3306 -d mysql:8.0 \
  --character-set-server=utf8mb4 \
  --collation-server=utf8mb4_unicode_ci
```

### 2. 启动后端

```bash
cd backend-laravel
composer install
php artisan migrate
php artisan serve --port=8000
```

### 3. 启动前端

```bash
cd frontend
npm install
npm run dev
```

前端开发服务器运行在 `http://localhost:5173`，API 请求自动代理到后端 `http://127.0.0.1:8000`。

### 4. 登录系统

| 角色 | 用户名 | 密码 |
|------|--------|------|
| 管理员 | `admin` | `admin123` |
| 教师 | `teacher1` | `teacher123` |

## API 接口概览

所有接口统一响应格式：

```json
{ "code": 0, "message": "ok", "data": {} }
```

| 方法 | 路径 | 说明 | 权限 |
|------|------|------|------|
| POST | `/api/auth/login` | 登录 | 无 |
| POST | `/api/auth/logout` | 登出 | 登录 |
| GET | `/api/auth/me` | 当前用户信息 | 登录 |
| GET | `/api/dashboard/stats` | 首页统计 | 登录 |
| GET | `/api/classes` | 班级列表 | 登录 |
| POST | `/api/classes` | 新增班级 | `class.manage` |
| PUT | `/api/classes/{id}` | 编辑班级 | `class.manage` |
| DELETE | `/api/classes/{id}` | 删除班级 | `class.manage` |
| GET | `/api/students` | 学生列表 | 登录 |
| POST | `/api/students` | 新增学生 | `student.manage` |
| PUT | `/api/students/{id}` | 编辑学生 | `student.manage` |
| POST | `/api/students/{id}/transfer` | 学生调班 | `student.manage` |
| DELETE | `/api/students/{id}` | 删除学生 | `student.manage` |
| GET | `/api/teachers` | 教师列表 | 登录 |
| POST | `/api/teachers` | 新增教师 | `teacher.manage` |
| PUT | `/api/teachers/{id}` | 编辑教师 | `teacher.manage` |
| DELETE | `/api/teachers/{id}` | 删除教师 | `teacher.manage` |
| GET | `/api/users` | 用户列表 | `user.manage` |
| POST | `/api/users` | 新增用户 | `user.manage` |
| GET | `/api/roles` | 角色列表 | `role.manage` |
| GET | `/api/roles/{id}/permissions` | 角色权限 | `role.manage` |
| PUT | `/api/roles/{id}/permissions` | 更新角色权限 | `role.manage` |
| GET | `/api/permissions` | 权限列表 | `role.manage` |
| GET | `/api/health` | 健康检查 | 无 |

## 数据库表

| 表名 | 说明 |
|------|------|
| `users` | 系统用户 |
| `roles` | 角色 |
| `permissions` | 权限 |
| `role_permissions` | 角色-权限关联 |
| `classes` | 班级 |
| `students` | 学生 |
| `teachers` | 教师 |
| `personal_access_tokens` | Sanctum Token |
