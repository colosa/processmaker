<?php
namespace TYPO3\ClassAliasLoader\Tests\Unit;

/*
 * This file is part of the class alias loader package.
 *
 * (c) Helmut Hummel <info@helhum.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use TYPO3\ClassAliasLoader\Config;

/**
 * Test case for Config
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Config
     */
    protected $subject;

    /**
     * @var IOInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $IOMock;

    /**
     * @var PackageInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $packageMock;

    public function setUp()
    {
        $this->IOMock = $this->getMock('Composer\\IO\\IOInterface');
        $this->packageMock = $this->getMock('Composer\\Package\\PackageInterface');

       $this->subject = new Config($this->packageMock, $this->IOMock);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionCode 1444039407
     */
    public function throwsExceptionForEmptyKey()
    {
        $this->subject->get(null);
    }

    /**
     * @test
     */
    public function defaultConfigIsAppliedWhenNothingIsConfiguredInPackage()
    {
        $this->assertFalse($this->subject->get('always-add-alias-loader'));
        $this->assertTrue($this->subject->get('autoload-case-sensitivity'));
        $this->assertNull($this->subject->get('class-alias-maps'));
    }

    /**
     * @test
     */
    public function aliasMapConfigIsExtracted()
    {
        $this->packageMock->expects($this->any())->method('getExtra')->willReturn(
            array(
                'typo3/class-alias-loader' => array(
                    'class-alias-maps' => array(
                        'path/map.php'
                    )
                )
            )
        );

        $subject = new Config($this->packageMock, $this->IOMock);

        $this->assertSame(array('path/map.php'), $subject->get('class-alias-maps'));
    }

    /**
     * @test
     */
    public function aliasMapConfigIsExtractedFromDeprecatedKey()
    {
        $this->packageMock->expects($this->any())->method('getExtra')->willReturn(
            array(
                'helhum/class-alias-loader' => array(
                    'class-alias-maps' => array(
                        'path/map.php'
                    )
                )
            )
        );
        $this->IOMock->expects($this->once())->method('writeError');

        $subject = new Config($this->packageMock, $this->IOMock);

        $this->assertSame(array('path/map.php'), $subject->get('class-alias-maps'));
    }

    /**
     * @test
     */
    public function otherConfigIsExtracted()
    {
        $this->packageMock->expects($this->any())->method('getExtra')->willReturn(
            array(
                'typo3/class-alias-loader' => array(
                    'always-add-alias-loader' => true,
                    'autoload-case-sensitivity' => false,
                )
            )
        );

        $subject = new Config($this->packageMock, $this->IOMock);

        $this->assertTrue($subject->get('always-add-alias-loader'));
        $this->assertFalse($subject->get('autoload-case-sensitivity'));
    }

    /**
     * @test
     */
    public function otherConfigIsExtractedFromDeprecatedKey()
    {
        $this->packageMock->expects($this->any())->method('getExtra')->willReturn(
            array(
                'helhum/class-alias-loader' => array(
                    'always-add-alias-loader' => true,
                    'autoload-case-sensitivity' => false,
                )
            )
        );

        $subject = new Config($this->packageMock, $this->IOMock);

        $this->assertTrue($subject->get('always-add-alias-loader'));
        $this->assertFalse($subject->get('autoload-case-sensitivity'));
    }

    /**
     * @test
     */
    public function caseSensitivityConfigIsExtractedFromVeryDeprecatedKey()
    {
        $this->packageMock->expects($this->any())->method('getExtra')->willReturn(
            array(
                'autoload-case-sensitivity' => false,
            )
        );

        $subject = new Config($this->packageMock, $this->IOMock);

        $this->assertFalse($subject->get('autoload-case-sensitivity'));
    }

}
