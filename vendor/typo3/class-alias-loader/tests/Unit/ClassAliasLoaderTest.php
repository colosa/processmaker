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

use Composer\Autoload\ClassLoader as ComposerClassLoader;
use TYPO3\ClassAliasLoader\ClassAliasLoader;

/**
 * Test case for ClassAliasLoader
 */
class ClassAliasLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClassAliasLoader
     */
    protected $subject;

    /**
     * @var ComposerClassLoader|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $composerClassLoaderMock;

    public function setUp()
    {
        $this->composerClassLoaderMock = $this->getMock('Composer\\Autoload\\ClassLoader');
        $this->subject = new ClassAliasLoader($this->composerClassLoaderMock);
    }

    public function tearDown()
    {
        $this->subject->unregister();
    }

    /**
     * @test
     */
    public function callingAnUnknownMethodWillBeProxiedToComposerClassLoader()
    {
        $this->composerClassLoaderMock->expects($this->once())->method('getUseIncludePath');
        $this->subject->getUseIncludePath();
    }

    /**
     * @test
     */
    public function registeringTheAliasLoaderUnregistersComposerClassLoader()
    {
        $this->composerClassLoaderMock->expects($this->once())->method('unregister');
        $this->subject->register();
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionCode 1422631610
     */
    public function callingAnUnknownMethodThatDoesNotExistInComposerClassLoaderThrowsException()
    {
        $this->subject->fooBar();
    }

    /**
     * @test
     */
    public function composerLoadClassIsCalledOnlyOnceWhenCaseSensitiveClassLoadingIsOn()
    {
        $this->composerClassLoaderMock->expects($this->once())->method('loadClass');
        $this->subject->loadClassWithAlias('TestClass');
    }

    /**
     * @test
     */
    public function composerLoadClassIsCalledOnlyOnceWhenCaseSensitiveClassLoadingIsOffButClassIsFound()
    {
        $this->composerClassLoaderMock->expects($this->once())->method('loadClass')->willReturn(true);
        $this->subject->setCaseSensitiveClassLoading(false);
        $this->subject->loadClassWithAlias('TestClass');
    }

    /**
     * @test
     */
    public function composerLoadClassIsCalledTwiceWhenCaseSensitiveClassLoadingIsOffAndClassIsNotFound()
    {
        $this->composerClassLoaderMock->expects($this->exactly(2))->method('loadClass');
        $this->subject->setCaseSensitiveClassLoading(false);
        $this->subject->loadClassWithAlias('TestClass');
    }

    /**
     * @test
     */
    public function loadsClassIfNoAliasIsFound()
    {
        $testClassName = 'TestClass' . md5(uniqid('bla', true));
        $this->composerClassLoaderMock->expects($this->once())->method('loadClass')->willReturnCallback(function ($className) {
            eval('class ' . $className . ' {}');
            return true;
        });
        $this->subject->loadClassWithAlias($testClassName);
        $this->assertTrue(class_exists($testClassName, false));
    }

    /**
     * @test
     */
    public function loadClassWithOriginalClassNameSetsAliases()
    {
        $testClassName = 'TestClass' . md5(uniqid('bla', true));
        $testAlias1 = 'TestAlias' . md5(uniqid('bla', true));
        $testAlias2 = 'TestAlias' . md5(uniqid('bla', true));

        $this->composerClassLoaderMock->expects($this->once())->method('loadClass')->willReturnCallback(function ($className) {
            eval('class ' . $className . ' {}');
            return true;
        });

        $this->subject->setAliasMap(array(
            'aliasToClassNameMapping' => array(
                strtolower($testAlias1) => $testClassName,
                strtolower($testAlias2) => $testClassName,
            ),
            'classNameToAliasMapping' => array(
                $testClassName => array(strtolower($testAlias1), strtolower($testAlias2))
            ),
        ));

        $this->subject->loadClassWithAlias($testClassName);
        $this->assertTrue(class_exists($testAlias1, false));
        $this->assertTrue(class_exists($testAlias2, false));
    }

    /**
     * @test
     */
    public function getClassNameForAliasReturnsClassNameForEachAlias()
    {
        $testClassName = 'TestClass' . md5(uniqid('bla', true));
        $testAlias1 = 'TestAlias' . md5(uniqid('bla', true));
        $testAlias2 = 'TestAlias' . md5(uniqid('bla', true));

        $this->subject->setAliasMap(array(
            'aliasToClassNameMapping' => array(
                strtolower($testAlias1) => $testClassName,
                strtolower($testAlias2) => $testClassName,
            ),
            'classNameToAliasMapping' => array(
                $testClassName => array(strtolower($testAlias1), strtolower($testAlias2))
            ),
        ));

        $this->assertEquals($testClassName, $this->subject->getClassNameForAlias($testAlias1));
        $this->assertEquals($testClassName, $this->subject->getClassNameForAlias($testAlias2));
    }

    /**
     * @test
     */
    public function addAliasMapAddsAliasesCorrectlyToTheMap()
    {
        $testClassName = 'TestClass' . md5(uniqid('bla', true));
        $testAlias1 = 'TestAlias' . md5(uniqid('bla', true));
        $testAlias2 = 'TestAlias' . md5(uniqid('bla', true));

        $this->subject->setAliasMap(array(
            'aliasToClassNameMapping' => array(
                strtolower($testAlias1) => $testClassName,
            ),
            'classNameToAliasMapping' => array(
                $testClassName => array(strtolower($testAlias1))
            ),
        ));

        $this->subject->addAliasMap(array(
            'aliasToClassNameMapping' => array(
                $testAlias2 => $testClassName,
            ),
        ));

        $this->assertEquals($testClassName, $this->subject->getClassNameForAlias($testAlias1));
        $this->assertEquals($testClassName, $this->subject->getClassNameForAlias($testAlias2));
    }

    /**
     * @test
     */
    public function getClassNameForAliasReturnsClassNameForClassName()
    {
        $testClassName = 'TestClass' . md5(uniqid('bla', true));
        $testAlias1 = 'TestAlias' . md5(uniqid('bla', true));
        $testAlias2 = 'TestAlias' . md5(uniqid('bla', true));

        $this->subject->setAliasMap(array(
            'aliasToClassNameMapping' => array(
                strtolower($testAlias1) => $testClassName,
                strtolower($testAlias2) => $testClassName,
            ),
            'classNameToAliasMapping' => array(
                $testClassName => array(strtolower($testAlias1), strtolower($testAlias2))
            ),
        ));

        $this->assertEquals($testClassName, $this->subject->getClassNameForAlias($testClassName));
    }

    /**
     * @test
     */
    public function getClassNameForAliasReturnsClassNameForClassNameWithNoAliasMapSet()
    {
        $testClassName = 'TestClass' . md5(uniqid('bla', true));
        $this->assertEquals($testClassName, $this->subject->getClassNameForAlias($testClassName));
    }

    /**
     * @test
     */
    public function loadClassWithAliasClassNameSetsAliasesAndLoadsOriginalClass()
    {
        $testClassName = 'TestClass' . md5(uniqid('bla', true));
        $testAlias1 = 'TestAlias' . md5(uniqid('bla', true));
        $testAlias2 = 'TestAlias' . md5(uniqid('bla', true));

        $this->composerClassLoaderMock->expects($this->once())->method('loadClass')->willReturnCallback(function ($className) {
            eval('class ' . $className . ' {}');
            return true;
        });

        $this->subject->setAliasMap(array(
            'aliasToClassNameMapping' => array(
                strtolower($testAlias1) => $testClassName,
                strtolower($testAlias2) => $testClassName,
            ),
            'classNameToAliasMapping' => array(
                $testClassName => array(strtolower($testAlias1), strtolower($testAlias2))
            ),
        ));

        $this->subject->loadClassWithAlias($testAlias1);
        $this->assertTrue(class_exists($testClassName, false), 'Class name is not loaded');
        $this->assertTrue(class_exists($testAlias1, false), 'First alias is not loaded');
        $this->assertTrue(class_exists($testAlias2, false), 'Second alias is not loaded');
    }

    /**
     * @test
     */
    public function aliasesInstancesHaveOriginalClassName()
    {
        $testClassName = 'TestClass' . md5(uniqid('bla', true));
        $testAlias1 = 'TestAlias' . md5(uniqid('bla', true));
        $testAlias2 = 'TestAlias' . md5(uniqid('bla', true));

        $this->composerClassLoaderMock->expects($this->once())->method('loadClass')->willReturnCallback(function ($className) {
            eval('class ' . $className . ' {}');
            return true;
        });

        $this->subject->setAliasMap(array(
            'aliasToClassNameMapping' => array(
                strtolower($testAlias1) => $testClassName,
                strtolower($testAlias2) => $testClassName,
            ),
            'classNameToAliasMapping' => array(
                $testClassName => array(strtolower($testAlias1), strtolower($testAlias2))
            ),
        ));

        $this->subject->loadClassWithAlias($testClassName);

        $testObject1 = new $testAlias1();
        $testObject2 = new $testAlias2();

        $this->assertSame($testClassName, get_class($testObject1));
        $this->assertSame($testClassName, get_class($testObject2));
    }

    /**
     * @test
     */
    public function classAliasesAreGracefullySetIfClassAlreadyExists()
    {
        $testClassName = 'TestClass' . md5(uniqid('bla', true));
        $testAlias1 = 'TestAlias' . md5(uniqid('bla', true));
        $testAlias2 = 'TestAlias' . md5(uniqid('bla', true));
        $this->composerClassLoaderMock->expects($this->never())->method('loadClass');

        $this->subject->setAliasMap(array(
            'aliasToClassNameMapping' => array(
                strtolower($testAlias1) => $testClassName,
                strtolower($testAlias2) => $testClassName,
            ),
            'classNameToAliasMapping' => array(
                $testClassName => array(strtolower($testAlias1), strtolower($testAlias2))
            ),
        ));

        eval('class ' . $testClassName . ' {}');

        $this->subject->loadClassWithAlias($testClassName);

        $testObject1 = new $testAlias1();
        $testObject2 = new $testAlias2();

        $this->assertSame($testClassName, get_class($testObject1));
        $this->assertSame($testClassName, get_class($testObject2));
    }

    /**
     * @test
     */
    public function interfaceAliasesAreGracefullySetIfInterfaceAlreadyExists()
    {
        $testClassName = 'TestClass' . md5(uniqid('bla', true));
        $testAlias1 = 'TestAlias' . md5(uniqid('bla', true));
        $testAlias2 = 'TestAlias' . md5(uniqid('bla', true));
        $this->composerClassLoaderMock->expects($this->never())->method('loadClass');

        $this->subject->setAliasMap(array(
            'aliasToClassNameMapping' => array(
                strtolower($testAlias1) => $testClassName,
                strtolower($testAlias2) => $testClassName,
            ),
            'classNameToAliasMapping' => array(
                $testClassName => array(strtolower($testAlias1), strtolower($testAlias2))
            ),
        ));

        eval('interface ' . $testClassName . ' {}');

        $this->subject->loadClassWithAlias($testClassName);

        $this->assertTrue(interface_exists($testAlias1, false), 'First alias is not loaded');
        $this->assertTrue(interface_exists($testAlias2, false), 'Second alias is not loaded');
    }

    /**
     * @test
     */
    public function classAliasesAreNotReEstablishedIfTheyAlreadyExist()
    {
        $testClassName = 'TestClass' . md5(uniqid('bla', true));
        $testAlias1 = 'TestAlias' . md5(uniqid('bla', true));
        $testAlias2 = 'TestAlias' . md5(uniqid('bla', true));
        $this->composerClassLoaderMock->expects($this->never())->method('loadClass');

        $this->subject->setAliasMap(array(
            'aliasToClassNameMapping' => array(
                strtolower($testAlias1) => $testClassName,
                strtolower($testAlias2) => $testClassName,
            ),
            'classNameToAliasMapping' => array(
                $testClassName => array(strtolower($testAlias1), strtolower($testAlias2))
            ),
        ));

        eval('class ' . $testClassName . ' {}');
        class_alias($testClassName, $testAlias1);


        $this->subject->loadClassWithAlias($testClassName);

        $this->assertTrue(class_exists($testAlias2, false), 'Second alias is not loaded');
    }

    /**
     * @test
     */
    public function loadClassWithAliasReturnsNullIfComposerClassLoaderCannotFindClass()
    {
        $this->composerClassLoaderMock->expects($this->once())->method('loadClass');
        $this->assertNull($this->subject->loadClassWithAlias('TestClass'));
    }

    /**
     * @test
     */
    public function loadClassWithAliasReturnsNullIfComposerClassLoaderCannotFindClassEvenIfItExistsInMap()
    {
        $testClassName = 'TestClass' . md5(uniqid('bla', true));
        $testAlias1 = 'TestAlias' . md5(uniqid('bla', true));
        $testAlias2 = 'TestAlias' . md5(uniqid('bla', true));

        $this->subject->setAliasMap(array(
            'aliasToClassNameMapping' => array(
                strtolower($testAlias1) => $testClassName,
                strtolower($testAlias2) => $testClassName,
            ),
            'classNameToAliasMapping' => array(
                $testClassName => array(strtolower($testAlias1), strtolower($testAlias2))
            ),
        ));

        $this->composerClassLoaderMock->expects($this->once())->method('loadClass');
        $this->assertNull($this->subject->loadClassWithAlias($testClassName));
    }

}
