<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\Alipay
 *
 * @property int $id
 * @property int $app_id 应用id
 * @property string $open_id 支付宝的app_id
 * @property string $ali_public_key 支付宝公钥，用于验签支付宝回调数据
 * @property string $private_key 应用私钥，用于下单生成签名
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder|Alipay newModelQuery()
 * @method static Builder|Alipay newQuery()
 * @method static Builder|Alipay query()
 * @method static Builder|Alipay whereAliPublicKey($value)
 * @method static Builder|Alipay whereAppId($value)
 * @method static Builder|Alipay whereCreatedAt($value)
 * @method static Builder|Alipay whereId($value)
 * @method static Builder|Alipay whereOpenId($value)
 * @method static Builder|Alipay wherePrivateKey($value)
 * @method static Builder|Alipay whereUpdatedAt($value)
 * @mixin \Eloquent
 * @mixin Builder
 */
class Alipay extends Model
{
    protected $table = 'alipay';
}