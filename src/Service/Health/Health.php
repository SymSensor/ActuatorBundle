<?php

declare(strict_types=1);

/*
 * This file is part of the symsensor/actuator-bundle package.
 *
 * (c) Kevin Studer <kreemer@me.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymSensor\ActuatorBundle\Service\Health;

final class Health implements HealthInterface
{
    private HealthState $status;

    /**
     * @var array<string, mixed>
     */
    private array $details;

    private ?string $error;

    /**
     * @param array<string, mixed> $details
     */
    public function __construct(HealthState $status, array $details = [], ?string $error = null)
    {
        $this->status = $status;
        $this->details = $details;
        $this->error = $error;
    }

    /**
     * @param array<string, mixed> $details
     */
    public static function up(array $details = []): self
    {
        return new self(HealthState::UP, $details);
    }

    public static function down(?string $error = null): self
    {
        return new self(HealthState::DOWN, [], $error);
    }

    public static function unknown(?string $error = null): self
    {
        return new self(HealthState::UNKNOWN, [], $error);
    }

    public function getStatus(): HealthState
    {
        return $this->status;
    }

    public function isUp(): bool
    {
        return HealthState::UP === $this->status;
    }

    /**
     * @param array<string, mixed> $details
     */
    public function setDetails(array $details): self
    {
        $this->details = $details;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getDetails(): array
    {
        return $this->details;
    }

    public function setError(?string $error): self
    {
        $this->error = $error;

        return $this;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * @return array<string, string|array<mixed>>
     */
    public function jsonSerialize(): array
    {
        $serialized = ['status' => $this->status->name];

        if (\count($this->details) > 0) {
            $serialized['details'] = $this->details;
        }

        if (null !== $this->error) {
            $serialized['error'] = $this->error;
        }

        return $serialized;
    }
}
