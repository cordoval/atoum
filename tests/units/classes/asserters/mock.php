<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters
;

require_once(__DIR__ . '/../../runner.php');

class dummy
{
	public function foo($bar) {}
}

class mock extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('mageekguy\atoum\asserter')
		;
	}

	public function test__construct()
	{
		$asserter = new asserters\mock($generator = new asserter\generator($this));

		$this->assert
			->object($asserter->getScore())->isIdenticalTo($this->getScore())
			->object($asserter->getLocale())->isIdenticalTo($this->getLocale())
			->object($asserter->getGenerator())->isIdenticalTo($generator)
			->variable($asserter->getMock())->isNull()
			->variable($asserter->getTestedMethodName())->isNull()
			->variable($asserter->getTestedMethodArguments())->isNull()
		;
	}

	public function testReset()
	{
		$this->mockGenerator
			->generate('mageekguy\atoum\score')
			->generate('mageekguy\atoum\mock\controller')
		;

		$mockController = new \mock\mageekguy\atoum\mock\controller();

		$asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->variable($asserter->getMock())->isNull()
			->object($asserter->reset())->isIdenticalTo($asserter)
			->variable($asserter->getMock())->isNull()
		;

		$asserter->setWith($mock = new \mock\mageekguy\atoum\score());
		$mock->setMockController($mockController);

		$this->assert
			->object($asserter->getMock())->isIdenticalTo($mock)
			->object($asserter->reset())->isIdenticalTo($asserter)
			->object($asserter->getMock())->isIdenticalTo($mock)
			->mock($mockController)
				->call('resetCalls')
		;

	}

	public function testSetWith()
	{
		$asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score())));

		$this->mockGenerator
			->generate(__CLASS__)
		;

		$adapter = new atoum\test\adapter();
		$adapter->class_exists = true;

		$this->assert
			->exception(function() use ($asserter, & $mock) {
						$asserter->setWith($mock = uniqid());
					}
				)
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is not a mock'), $asserter->getTypeOf($mock)))
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isZero()
			->object($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\mock(null, null, $adapter)))->isIdenticalTo($asserter)
			->object($asserter->getMock())->isIdenticalTo($mock)
		;
	}

	public function testWasCalled()
	{
		$asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->wasCalled();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
		;

		$this->mockGenerator
			->generate(__CLASS__)
		;

		$adapter = new atoum\test\adapter();
		$adapter->class_exists = true;

		$asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\mock(null, null, $adapter));

		$mock->getMockController()->resetCalls();

		$score->reset();

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->wasCalled(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is not called'), get_class($mock)))
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::wasCalled()',
						'fail' => sprintf($test->getLocale()->_('%s is not called'), get_class($mock))
					)
				)
			)
		;

		$score->reset();

		$this->assert
			->exception(function() use (& $line, $asserter, & $failMessage) { $line = __LINE__; $asserter->wasCalled($failMessage = uniqid()); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage($failMessage)
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::wasCalled()',
						'fail' => $failMessage
					)
				)
			)
		;

		$mock->getMockController()->{__FUNCTION__} = function() {};
		$mock->{__FUNCTION__}();

		$this->assert
			->object($asserter->wasCalled())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
		;
	}

	public function testWasNotCalled()
	{
		$asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->wasNotCalled();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
		;

		$this->mockGenerator
			->generate(__CLASS__)
		;

		$adapter = new atoum\test\adapter();
		$adapter->class_exists = true;

		$asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\mock(null, null, $adapter));

		$mock->getMockController()->resetCalls();

		$score->reset();

		$this->assert
			->object($asserter->wasNotCalled())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$mock->getMockController()->{__FUNCTION__} = function() {};
		$mock->{__FUNCTION__}();

		$this->assert
			->exception(function() use (& $line, $asserter, & $failMessage) { $line = __LINE__; $asserter->wasNotCalled($failMessage = uniqid()); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage($failMessage)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::wasNotCalled()',
						'fail' => $failMessage
					)
				)
			)
		;
	}

	public function testCall()
	{
		$asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->call(uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
		;

		$this->mockGenerator
			->generate('mageekguy\atoum\tests\units\asserters\dummy')
		;

		$asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy());

		$this->assert
			->object($asserter->call($method = uniqid()))->isIdenticalTo($asserter)
			->string($asserter->getTestedMethodName())->isEqualTo($method)
		;

		$asserter->withArguments();

		$this->assert
			->variable($asserter->getTestedMethodArguments())->isNotNull()
			->object($asserter->call($method = uniqid()))->isIdenticalTo($asserter)
			->string($asserter->getTestedMethodName())->isEqualTo($method)
			->variable($asserter->getTestedMethodArguments())->isNull()
		;
	}

	public function testWithArguments()
	{
		$asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->withArguments(uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
		;

		$this->mockGenerator
			->generate('mageekguy\atoum\tests\units\asserters\dummy')
		;

		$asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy());

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->withArguments(uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called method is undefined')
		;

		$asserter->call(uniqid());

		$this->assert
			->object($asserter->withArguments())->isIdenticalTo($asserter)
			->array($asserter->getTestedMethodArguments())->isEmpty()
			->object($asserter->withArguments($arg1 = uniqid()))->isIdenticalTo($asserter)
			->array($asserter->getTestedMethodArguments())->isEqualTo(array($arg1))
			->object($asserter->withArguments($arg1 = uniqid(), $arg2 = uniqid()))->isIdenticalTo($asserter)
			->array($asserter->getTestedMethodArguments())->isEqualTo(array($arg1, $arg2))
		;
	}

	public function testOnce()
	{
		$asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->once();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
		;

		$this->mockGenerator
			->generate('mageekguy\atoum\tests\units\asserters\dummy')
		;

		$asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy());

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->once();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called method is undefined')
		;

		$asserter->call('foo');

		$score->reset();

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->once(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s::%s() is called 0 time instead of 1'), get_class($mock), 'foo'))
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::once()',
						'fail' => sprintf($test->getLocale()->_('method %s::%s() is called 0 time instead of 1'), get_class($mock), 'foo')
					)
				)
			)
		;

		$mock->foo(uniqid());

		$this->assert
			->object($asserter->once())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$mock->foo(uniqid());

		$this->assert
			->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->once(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s::%s() is called 2 times instead of 1'), get_class($mock), 'foo'))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(2)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::once()',
						'fail' => sprintf($test->getLocale()->_('method %s::%s() is called 0 time instead of 1'), get_class($mock), 'foo')
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $otherLine,
						'asserter' => get_class($asserter) . '::once()',
						'fail' => sprintf($test->getLocale()->_('method %s::%s() is called 2 times instead of 1'), get_class($mock), 'foo')
					)
				)
			)
		;

		$mock->getMockController()->resetCalls();

		$score->reset();

		$asserter->withArguments($arg = uniqid());

		$mock->foo($arg);

		$this->assert
			->object($asserter->once())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$asserter->withArguments(uniqid());

		$this->assert
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->once(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s::%s() is called 0 time instead of 1'), get_class($mock), 'foo'))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::once()',
						'fail' => sprintf($test->getLocale()->_('method %s::%s() is called 0 time instead of 1'), get_class($mock), 'foo')
					)
				)
			)
		;
	}

	public function testAtLeastOnce()
	{
		$asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->atLeastOnce();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
		;

		$this->mockGenerator
			->generate('mageekguy\atoum\tests\units\asserters\dummy')
		;

		$asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy());

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->atLeastOnce();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called method is undefined')
		;

		$asserter->call('foo');

		$score->reset();

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->atLeastOnce(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s::%s() is called 0 time'), get_class($mock), 'foo'))
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::atLeastOnce()',
						'fail' => sprintf($test->getLocale()->_('method %s::%s() is called 0 time'), get_class($mock), 'foo')
					)
				)
			)
		;

		$mock->foo(uniqid());

		$this->assert
			->object($asserter->atLeastOnce())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$mock->foo(uniqid());

		$this->assert
			->object($asserter->atLeastOnce())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(2)
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$mock->getMockController()->resetCalls();

		$score->reset();

		$asserter->withArguments($arg = uniqid());

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->atLeastOnce(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s::%s() is called 0 time'), get_class($mock), 'foo'))
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::atLeastOnce()',
						'fail' => sprintf($test->getLocale()->_('method %s::%s() is called 0 time'), get_class($mock), 'foo')
					)
				)
			)
		;

		$mock->foo($arg);

		$this->assert
			->object($asserter->atLeastOnce())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$asserter->withArguments(uniqid());

		$this->assert
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
			->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->atLeastOnce(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s::%s() is called 0 time'), get_class($mock), 'foo'))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(2)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::atLeastOnce()',
						'fail' => sprintf($test->getLocale()->_('method %s::%s() is called 0 time'), get_class($mock), 'foo')
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $otherLine,
						'asserter' => get_class($asserter) . '::atLeastOnce()',
						'fail' => sprintf($test->getLocale()->_('method %s::%s() is called 0 time'), get_class($mock), 'foo')
					)
				)
			)
		;
	}

	public function testExactly()
	{
		$asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->exactly(2);
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
		;

		$this->mockGenerator
			->generate('mageekguy\atoum\tests\units\asserters\dummy')
		;

		$asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy());

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->exactly(2);
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called method is undefined')
		;

		$asserter->call('foo');

		$score->reset();

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->exactly(2); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s::%s() is called 0 time instead of 2'), get_class($mock), 'foo'))
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('method %s::%s() is called 0 time instead of 2'), get_class($mock), 'foo')
					)
				)
			)
		;

		$mock->foo(uniqid());

		$this->assert
			->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->exactly(2); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s::%s() is called 1 time instead of 2'), get_class($mock), 'foo'))
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isEqualTo(2)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('method %s::%s() is called 0 time instead of 2'), get_class($mock), 'foo')
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $otherLine,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('method %s::%s() is called 1 time instead of 2'), get_class($mock), 'foo')
					)
				)
			)
		;

		$mock->foo(uniqid());

		$this->assert
			->object($asserter->exactly(2))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(2)
		;

		$mock->foo(uniqid());

		$this->assert
			->exception(function() use (& $anotherLine, $asserter) { $anotherLine = __LINE__; $asserter->exactly(2); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s::%s() is called 3 times instead of 2'), get_class($mock), 'foo'))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(3)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('method %s::%s() is called 0 time instead of 2'), get_class($mock), 'foo')
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $otherLine,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('method %s::%s() is called 1 time instead of 2'), get_class($mock), 'foo')
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $anotherLine,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('method %s::%s() is called 3 times instead of 2'), get_class($mock), 'foo')
					)
				)
			)
		;

		$mock->getMockController()->resetCalls();

		$score->reset();

		$asserter->withArguments($arg = uniqid());

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->exactly(2); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s::%s() is called 0 time instead of 2'), get_class($mock), 'foo'))
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('method %s::%s() is called 0 time instead of 2'), get_class($mock), 'foo')
					)
				)
			)
		;

		$mock->foo(uniqid());

		$this->assert
			->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->exactly(2); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s::%s() is called 0 time instead of 2'), get_class($mock), 'foo'))
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isEqualTo(2)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('method %s::%s() is called 0 time instead of 2'), get_class($mock), 'foo')
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $otherLine,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('method %s::%s() is called 0 time instead of 2'), get_class($mock), 'foo')
					)
				)
			)
		;

		$mock->foo($arg);

		$this->assert
			->exception(function() use (& $anotherLine, $asserter) { $anotherLine = __LINE__; $asserter->exactly(2); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s::%s() is called 1 time instead of 2'), get_class($mock), 'foo'))
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isEqualTo(3)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('method %s::%s() is called 0 time instead of 2'), get_class($mock), 'foo')
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $otherLine,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('method %s::%s() is called 0 time instead of 2'), get_class($mock), 'foo')
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $anotherLine,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('method %s::%s() is called 1 time instead of 2'), get_class($mock), 'foo')
					)
				)
			)
		;

		$mock->foo($arg);

		$this->assert
			->object($asserter->exactly(2))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(3)
		;

		$mock->foo($arg);

		$this->assert
			->exception(function() use (& $anAnotherLine, $asserter) { $anAnotherLine = __LINE__; $asserter->exactly(2); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s::%s() is called 3 times instead of 2'), get_class($mock), 'foo'))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(4)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('method %s::%s() is called 0 time instead of 2'), get_class($mock), 'foo')
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $otherLine,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('method %s::%s() is called 0 time instead of 2'), get_class($mock), 'foo')
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $anotherLine,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('method %s::%s() is called 1 time instead of 2'), get_class($mock), 'foo')
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $anAnotherLine,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('method %s::%s() is called 3 times instead of 2'), get_class($mock), 'foo')
					)
				)
			)
		;
	}

	public function testNever()
	{
		$asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->never();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
		;

		$this->mockGenerator
			->generate('mageekguy\atoum\tests\units\asserters\dummy')
		;

		$asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy());

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->never();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called method is undefined')
		;

		$asserter->call('foo');

		$score->reset();

		$this->assert
			->object($asserter->never())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$mock->foo(uniqid());

		$this->assert
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->never(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s::%s() is called 1 time instead of 0'), get_class($mock), 'foo'))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::never()',
						'fail' => sprintf($test->getLocale()->_('method %s::%s() is called 1 time instead of 0'), get_class($mock), 'foo')
					)
				)
			)
		;

		$mock->getMockController()->resetCalls();

		$score->reset();

		$asserter->withArguments($arg = uniqid());

		$this->assert
			->object($asserter->never())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$mock->foo($arg);

		$this->assert
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->never(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s::%s() is called 1 time instead of 0'), get_class($mock), 'foo'))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::never()',
						'fail' => sprintf($test->getLocale()->_('method %s::%s() is called 1 time instead of 0'), get_class($mock), 'foo')
					)
				)
			)
		;

		$mock->foo($arg);

		$this->assert
			->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->never(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s::%s() is called 2 times instead of 0'), get_class($mock), 'foo'))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(2)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::never()',
						'fail' => sprintf($test->getLocale()->_('method %s::%s() is called 1 time instead of 0'), get_class($mock), 'foo')
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $otherLine,
						'asserter' => get_class($asserter) . '::never()',
						'fail' => sprintf($test->getLocale()->_('method %s::%s() is called 2 times instead of 0'), get_class($mock), 'foo')
					)
				)
			)
		;

		$asserter->withArguments(uniqid());

		$this->assert
			->object($asserter->never())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(2)
			->integer($score->getFailNumber())->isEqualTo(2)
		;
	}
}

?>
