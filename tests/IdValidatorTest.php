<?php
/**
 * Created by PhpStorm.
 * User: Chendujin
 * Date: 2018/9/6
 * Time: 21:51.
 */

namespace Chendujin\IdValidator\Tests;

use Chendujin\IdValidator\IdValidator;
use PHPUnit\Framework\TestCase;

class IdValidatorTest extends TestCase
{
    public function testIsValid()
    {
        $idValidator = new IdValidator();
        $this->assertFalse($idValidator->isValid('44030819990110'));     // 号码位数不合法
        $this->assertFalse($idValidator->isValid('111111199901101512')); // 地址码不合法
        $this->assertFalse($idValidator->isValid('440308199902301512')); // 出生日期码不合法
        $this->assertFalse($idValidator->isValid('440308199901101513')); // 验证码不合法
        $this->assertFalse($idValidator->isValid('610104620932690'));    // 出生日期码不合法
        $this->assertFalse($idValidator->isValid('11010119900307867X')); // 校验位不合法
        $this->assertFalse($idValidator->isValid('500154199301135886', true)); // 出生日期在地址码发布之前，非严格模式
        $this->assertTrue($idValidator->isValid('500154199301135886', false)); // 出生日期在地址码发布之前，严格模式
        $this->assertTrue($idValidator->isValid('110101199003078670'));
        $this->assertTrue($idValidator->isValid('440308199901101512'));
        $this->assertTrue($idValidator->isValid('500154199804106120'));
        $this->assertFalse($idValidator->isValid('411082198901010002', true)); // 严格模式
        $this->assertTrue($idValidator->isValid('411082198901010002', false)); // 非严格模式：https://github.com/jxlwqq/id-validator/issues/53
        $this->assertTrue($idValidator->isValid('610104620927690'));
        $this->assertTrue($idValidator->isValid('810000199408230021')); // 港澳居民居住证 18 位
        $this->assertTrue($idValidator->isValid('830000199201300022')); // 台湾居民居住证 18 位
        $this->assertTrue($idValidator->isValid('44040119580101000X')); // 历史遗留数据：珠海市市辖区
        $this->assertTrue($idValidator->isValid('140120197901010008')); // 历史遗留数据：太原市市区
        $this->assertTrue($idValidator->isValid('441282198101011230')); // 历史遗留数据：广东省肇庆市罗定市
    }

    public function testFakeId()
    {
        $idValidator = new IdValidator();
        for ($i = 0; $i < 10000; $i++) {
            $this->assertTrue($idValidator->isValid($idValidator->fakeId()));
        }
        $this->assertTrue($idValidator->isValid($idValidator->fakeId(false)));
        $this->assertTrue($idValidator->isValid($idValidator->fakeId(true, '上海市', '2000', 1)));
        $this->assertTrue($idValidator->isValid($idValidator->fakeId(true, '黄浦区', '2001', 0)));
        $this->assertTrue($idValidator->isValid($idValidator->fakeId(true, '江苏省', '200001', 1)));
        $this->assertTrue($idValidator->isValid($idValidator->fakeId(true, '南京市', '2002', 0)));
        $this->assertTrue($idValidator->isValid($idValidator->fakeId(true, '秦淮区', '2003', 0)));
        $this->assertTrue($idValidator->isValid($idValidator->fakeId(true, '台湾省', '20181010', 0)));
        $this->assertTrue($idValidator->isValid($idValidator->fakeId(true, '香港特别行政区', '20181010', 1)));
        $this->assertTrue($idValidator->isValid($idValidator->fakeId(true, '澳门特别行政区', '20181111', 0)));
    }

    public function testGetInfo()
    {
        $idValidator = new IdValidator();
        $this->assertEquals(
            [
                'addressCode'   => '440308',
                'abandoned'     => 0,
                'address'       => '广东省深圳市盐田区',
                'addressTree'   => ['广东省', '深圳市', '盐田区'],
                'birthdayCode'  => '1999-01-10',
                'constellation' => '摩羯座',
                'chineseZodiac' => '卯兔',
                'sex'           => 1,
                'length'        => 18,
                'checkBit'      => '2', ],
            $idValidator->getInfo('440308199901101512')
        );

        $this->assertEquals(
            [
                'addressCode'   => '362324',
                'abandoned'     => 1,
                'address'       => '江西省宜春地区丰城县',
                'addressTree'   => ['江西省', '宜春地区', '丰城县'],
                'birthdayCode'  => '1980-01-01',
                'constellation' => '摩羯座',
                'chineseZodiac' => '申猴',
                'sex'           => 1,
                'length'        => 18,
                'checkBit'      => '4',
            ],
            $idValidator->getInfo('362324198001010014')
        );

        $this->assertEquals(
            [
                'addressCode'   => '362324',
                'abandoned'     => 1,
                'address'       => '江西省宜春地区丰城县',
                'addressTree'   => ['江西省', '宜春地区', '丰城县'],
                'birthdayCode'  => '1981-01-01',
                'constellation' => '摩羯座',
                'chineseZodiac' => '酉鸡',
                'sex'           => 1,
                'length'        => 18,
                'checkBit'      => '1', ],
            $idValidator->getInfo('362324198101010011')
        );

        $this->assertEquals(
            [
                'addressCode'   => '362324',
                'abandoned'     => 1,
                'address'       => '江西省上饶地区铅山县',
                'addressTree'   => ['江西省', '上饶地区', '铅山县'],
                'birthdayCode'  => '1982-01-01',
                'constellation' => '摩羯座',
                'chineseZodiac' => '戌狗',
                'sex'           => 1,
                'length'        => 18,
                'checkBit'      => '9',
            ],
            $idValidator->getInfo('362324198201010019')
        );

        $this->assertFalse($idValidator->isValid('440308199901101513'));

        $this->assertEquals(
            [
                'addressCode'   => '610104',
                'abandoned'     => 0,
                'address'       => '陕西省西安市莲湖区',
                'addressTree'   => ['陕西省', '西安市', '莲湖区'],
                'birthdayCode'  => '1962-09-27',
                'constellation' => '天秤座',
                'chineseZodiac' => '寅虎',
                'sex'           => 0,
                'length'        => 15,
                'checkBit'      => '', ],
            $idValidator->getInfo('610104620927690')
        );
        $this->assertFalse($idValidator->isValid('610104620932690'));

        $this->assertEquals(
            [
                'addressCode'   => '430302',
                'abandoned'     => 0,
                'address'       => '湖南省湘潭市雨湖区',
                'addressTree'   => ['湖南省', '湘潭市', '雨湖区'],
                'birthdayCode'  => '1993-12-19',
                'constellation' => '射手座',
                'chineseZodiac' => '酉鸡',
                'sex'           => 1,
                'length'        => 18,
                'checkBit'      => '9', ],
            $idValidator->getInfo('430302199312194239')
        );

        // 非严格模式下，合法
        $this->assertEquals(
            [
                'addressCode'   => '411082',
                'abandoned'     => 0,
                'address'       => '河南省许昌市长葛市',
                'addressTree'   => ['河南省', '许昌市', '长葛市'],
                'birthdayCode'  => '1989-01-01',
                'constellation' => '摩羯座',
                'chineseZodiac' => '巳蛇',
                'sex'           => 0,
                'length'        => 18,
                'checkBit'      => '2', ],
            $idValidator->getInfo('411082198901010002')
        );

        // 严格模式下，非法
        $this->assertFalse($idValidator->getInfo('411082198901010002', true));

        // 历史遗留数据：珠海市市辖区
        $this->assertEquals([
            'addressCode'   => '440401',
            'abandoned'     => 1,
            'address'       => '广东省珠海市市辖区',
            'addressTree'   => ['广东省', '珠海市', '市辖区'],
            'birthdayCode'  => '1958-01-01',
            'constellation' => '摩羯座',
            'chineseZodiac' => '戌狗',
            'sex'           => 0,
            'length'        => 18,
            'checkBit'      => 'X',
        ], $idValidator->getInfo('44040119580101000X'));

        // 历史遗留数据：太原市市区
        $this->assertEquals([
            'addressCode'   => '140120',
            'abandoned'     => 1,
            'address'       => '山西省太原市市区',
            'addressTree'   => ['山西省', '太原市', '市区'],
            'birthdayCode'  => '1979-01-01',
            'constellation' => '摩羯座',
            'chineseZodiac' => '未羊',
            'sex'           => 0,
            'length'        => 18,
            'checkBit'      => '8',
        ], $idValidator->getInfo('140120197901010008'));
    }

    public function testUpgradeId()
    {
        $idValidator = new IdValidator();
        $this->assertEquals('610104196209276908', $idValidator->upgradeId('610104620927690'));
    }
}
