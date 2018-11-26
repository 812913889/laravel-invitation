# Laravel 邀請碼

## 介紹

此套件可用來實作邀請碼管理，每個會員可能都會有一個邀請碼，或是多個邀請碼

本專案參考 [clarkeash/doorman](https://github.com/clarkeash/doorman) 完成，因為作者有些限制與我的需求不合(比方說沒有邀請碼製作者、擁有者、狀態、類型等..)，因此此套件參考他的套件擴充，並儘量改成更彈性的使用

## 安裝

    以 composer 安裝

    composer require ariby/invitation
    
## config

    table 名稱定義於 config `laravel_invitation` 檔案中，可發佈後進行修改 `invite_table_name` 欄位，預設為`invites`
    
    另一個可修改參數是邀請碼長度 `code_length`，預設為 5
    
    
## Table 結構

        |   欄位名稱   |   說明 
        |--------------------------------------------------------------------------
        |    code     |  邀請碼  
        |--------------------------------------------------------------------------
        |   status    | 邀請碼的開放狀態 (enum => ['enabled', 'disabled']) ->default('enabled')
        |--------------------------------------------------------------------------
        |    for      | 邀請碼的專屬使用者 (null 表示所有人都可以使用)
        |--------------------------------------------------------------------------
        |  belong_to  | 邀請碼的擁有者 (nullable)
        |--------------------------------------------------------------------------
        |   made_by   | 邀請碼的製作者 (nullable)
        |--------------------------------------------------------------------------
        |    max      | 邀請碼的最大使用次數 (null 表示無限)
        |--------------------------------------------------------------------------
        |    uses     | 邀請碼的已使用次數 ->default('0')
        |--------------------------------------------------------------------------
        |    type     | 邀請碼的類型 (nullable)
        |--------------------------------------------------------------------------
        | valid_until | 邀請碼的有效期限 (null 表示永久)
        
## ORM 可使用函式

    $model->isExpired() => 回傳是否已過期
    
    $model->isEnabled() => 回傳是否狀態為開放使用
    
    $model->isFull() => 回傳是否已超過最大使用次數
    
    $model->isRestricted() => 回傳此推廣碼是否有綁定特定使用者
    
    $model->isRestrictedFor($userId) => 回傳此邀請碼是否為此會員專屬
    
    $model->isUseless() => 回傳此邀請碼是否已無法使用(超過使用次數或已過期)
    
## ORM Query Scope

    $builder->expired() // 取得已過期的邀請碼
    
    $builder->full() // 取得已超過使用次數上限的邀請碼
    
    $bulider->useless() // 取得已無法使用的邀請碼(超過使用次數或已過期)
    
## 建立邀請碼

    使用範例
    
    ```
    $inviteCode = LaravelInvitation::generate()
                    ->setCode($code) // 指定邀請碼，若不使用會自動隨機產生
                    ->status('enabled') // 是否開放使用，不使用預設為 enabled
                    ->belongTo($this->id) // 屬於特定代理 id
                    ->madeBy($this->id) // 製作者 id
                    ->expiresOn('2018-11-26 12:00:00') // 過期日期
                    ->type($type) // 此邀請碼類型
                    ->make()
                    ->first();
    ```
## 命令與排程

    排程清除已經過期的邀請碼
    
    `php artisan routine-clear:clear-expired-invite-codes`
    
    或是將此命令加入 kernel 排程
    
    ```
        protected function schedule(Schedule $schedule)
        {
            ...
            // 每天清除已過期的邀請碼
            $schedule->command('routine-clear:clear-expired-invite-codes')->daily();
            ...
        }
    ```