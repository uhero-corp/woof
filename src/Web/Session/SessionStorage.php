<?php

namespace Woof\Web\Session;

use InvalidArgumentException;
use LogicException;
use Woof\Http\Request;
use Woof\Web\Session;
use Woof\System\Clock;
use Woof\System\Random;

class SessionStorage
{
    /**
     * @var SessionContainer
     */
    private $container;

    /**
     * @var string
     */
    private $key;

    /**
     * @var int
     */
    private $maxAge;

    /**
     * @var float
     */
    private $gcProbability;

    /**
     * @var Clock
     */
    private $clock;

    /**
     * @var Random
     */
    private $random;

    /**
     * @var Session[]
     */
    private $sessions;

    private function __construct()
    {
        $this->sessions = [];
    }

    /**
     * このメソッドは SessionStorageBuilder::build() から参照されます。
     *
     * @param SessionStorageBuilder $builder
     * @return SessionStorage
     */
    public static function newInstance(SessionStorageBuilder $builder): self
    {
        if (!$builder->hasSessionContainer()) {
            throw new LogicException("SessionContainer is not specified");
        }
        if (!strlen($key = $builder->getKey())) {
            throw new LogicException("Session key is not specified");
        }

        $instance                = new self();
        $instance->container     = $builder->getSessionContainer();
        $instance->key           = $key;
        $instance->maxAge        = $builder->getMaxAge();
        $instance->gcProbability = $builder->getGcProbability();
        $instance->clock         = $builder->getClock();
        $instance->random        = $builder->getRandom();
        return $instance;
    }

    /**
     * @return SessionContainer
     */
    public function getSessionContainer(): SessionContainer
    {
        return $this->container;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return int
     */
    public function getMaxAge(): int
    {
        return $this->maxAge;
    }

    /**
     * @return float
     */
    public function getGcProbability(): float
    {
        return $this->gcProbability;
    }

    /**
     * 指定された HTTP リクエストに紐付けられたセッションを取得します。
     * もしも HTTP リクエストで指定されたセッション ID が無効または期限切れだった場合、
     * 新しいセッション ID を生成してその結果を返します。
     *
     * @param Request $request
     * @return Session
     */
    public function getSession(Request $request): Session
    {
        $id = $request->getCookie($this->key);
        return Session::validateId($id) ? ($this->sessions[$id] ?? $this->fetchSession($id)) : $this->newSession();
    }

    /**
     * @param string $id
     * @return Session
     */
    private function fetchSession(string $id): Session
    {
        $maxAge    = $this->maxAge;
        $container = $this->container;
        if ($this->determineGC()) {
            $container->cleanExpiredSessions($maxAge);
        }

        $contains = $container->contains($id, $maxAge);
        $fixedId  = $contains ? $id : $this->generateId();
        $isNew    = !$contains;
        $data     = $container->load($fixedId);
        $session  = new Session($fixedId, $data, $isNew);

        $this->sessions[$id] = $session;
        return $session;
    }

    /**
     * @return bool
     */
    private function determineGC(): bool
    {
        $p = $this->gcProbability;
        if ($p === 0.0) {
            return false;
        }
        if ($p === 1.0) {
            return true;
        }
        return ($this->random->next() / mt_getrandmax()) < $p;
    }

    /**
     * 指定された ID のセッションを取得します。
     * もしも対象のセッションが存在しない場合、引数のセッション ID でセッションを初期化します。
     *
     * @param string $id セッション ID
     * @return Session 引数のセッション ID を持つ Session オブジェクト
     * @throws InvalidArgumentException 指定された ID の書式が不正な場合
     */
    public function getSessionById($id)
    {
        if (!Session::validateId($id)) {
            throw new InvalidArgumentException("Invalid session ID: '{$id}'");
        }

        $maxAge    = $this->maxAge;
        $container = $this->container;
        $container->cleanExpiredSessions($maxAge);
        $isNew     = !$container->contains($id, $maxAge);
        $data      = $container->load($id);
        return new Session($id, $data, $isNew);
    }

    /**
     * @return bool
     */
    public function save(Session $session): bool
    {
        return $this->container->save($session->getId(), $session->getAll());
    }

    /**
     * @return Session
     */
    private function newSession()
    {
        $id     = $this->generateId();
        $result = new Session($id, [], true);

        $this->sessions[$id] = $result;
        return $result;
    }

    /**
     * @return string
     */
    private function generateId()
    {
        return sha1($this->key . $this->clock->getTime() . $this->random->next());
    }
}
