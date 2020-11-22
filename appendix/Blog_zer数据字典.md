#Zer个人博客数据字典
--------------------
[-->点我到百度][1]
[1]: http://www.baidu.com

##*1、普通用户表(blog_user)

|字段名|字段类型|长度|是否为空|约束|默认值|备注|
|---|---|---|---|---|---|---|
|user_id|int|11|N|主键|1|用户表主键|
|user_name|varchar|60|N||' '|用户名|
|user_pass|varchar|255|N||' '|密码|
|phone|int|11|Y||''|手机号|
|email|varchar|255|Y||' '|邮箱|
|created_at|datetime|13|Y||' '|用户注册时间|
|status|int|11|Y||' '|用户状态：1为启用；0为禁用；|
|active|int|4|Y||' '|账号是否激活 0：未激活，1：激活|
|token|varchar|255|Y||' '|验证账号有效性|
|expire|varchar|255|Y||' '|账号激活是否过期时间|

##*2、管理员用户表

|字段名|字段类型|长度|是否为空|约束|默认值|备注|
|---|---|---|---|---|---|---|
|admin_id|int|10|N|主键|Null|管理员用户表主键|
|admin_name|varchar|60|N|唯一约束|''|用户名|
|password|varchar|60|N||''|密码|

##*3、文章表

|字段名|字段类型|长度|是否为空|约束|默认值|备注|
|---|---|---|---|---|---|---|
|art_id|int|11|N|主键|1|文章表主键|
|art_title|varchar|255|N||''|文章标题|
|art_tag|varchar|255|N||''|文章标签|
|art_description|datetime|13|Y||' '|文章描述|
|art_thumb|datetime|13|Y||' '|最近更新时间|
|art_content|datetime|13|Y||' '|用户注销时间|
|art_time|int|1|Y||' '|用户状态：1为使用中；0为已注销；|
|art_editor|datetime|13|Y||' '|用户注销时间|
|art_view|int|1|Y||' '|用户状态：1为使用中；0为已注销；|
|cate_id|datetime|13|Y||' '|用户注销时间|
|created_at|int|1|Y||' '|用户状态：1为使用中；0为已注销；|
|updated_at|datetime|13|Y||' '|用户注销时间|
|delete_at|int|1|Y||' '|用户状态：1为使用中；0为已注销；|



