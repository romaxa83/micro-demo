<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use App\Auth\Service\PasswordHasher;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="auth_users")
 */
class User
{
    /**
     * @ORM\Column(type="auth_user_id")
     * @ORM\Id
     */
    private Id $id;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $date;

    /**
     * @ORM\Column(type="auth_user_email", unique=true)
     */
    private Email $email;

    /**
     * @ORM\Column(type="string",  nullable=true)
     */
    private ?string $passwordHash = null;

    /**
     * @ORM\Embedded(class="Token")
     */
    private ?Token $signUpConfirmToken = null;

    /**
     * @ORM\Embedded(class="Token")
     */
    private ?Token $passwordResetToken = null;

    /**
     * @ORM\Column(type="auth_user_status", length=16)
     */
    private Status $status;

    /**
     * @ORM\Column(type="auth_user_email", nullable=true)
     */
    private ?Email $newEmail = null;

    /**
     * @ORM\Embedded(class="Token")
     */
    private ?Token $newEmailToken = null;

    /**
     * @ORM\Column(type="auth_user_role", length=16)
     */
    private Role $role;

    /**
     * @ORM\OneToMany(targetEntity="UserNetwork", mappedBy="user", cascade={"all"}, orphanRemoval=true)
     */
    private Collection $networks;

    public function __construct(
        Id $id,
        \DateTimeImmutable $date,
        Email $email,
        Status $status
    )
    {
        $this->id = $id;
        $this->date = $date;
        $this->email = $email;
        $this->status = $status;
        $this->role = Role::user();
        $this->networks = new ArrayCollection();
    }

    public static function requestSignUpByEmail(
        Id $id,
        \DateTimeImmutable $date,
        Email $email,
        string $passwordHash,
        Token $token
    ):self
    {
        $user = new self($id, $date, $email, Status::wait());
        $user->passwordHash = $passwordHash;
        $user->signUpConfirmToken = $token;

        return $user;
    }

    public static function signUpByNetwork(
        Id $id,
        \DateTimeImmutable $date,
        Email $email,
        Network $network
    ):self
    {
        $user = new self($id, $date, $email, Status::active());
        $user->networks->add(new UserNetwork($user, $network));

        return $user;
    }

    public function confirmSignUp(string $token, \DateTimeImmutable $date): void
    {
        if($this->signUpConfirmToken == null){
            throw new \DomainException('Confirmation is not required.');
        }

        $this->signUpConfirmToken->validate($token, $date);
        $this->status = Status::active();
        $this->signUpConfirmToken = null;
    }

    public function requestPasswordReset(Token $token, \DateTimeImmutable $date)
    {
        if(!$this->isActive()){
            throw new \DomainException('User is not active.');
        }
        if($this->passwordResetToken !== null && !$this->passwordResetToken->isExpiredTo($date)){
            throw new \DomainException('Resetting is already requested.');
        }
        $this->passwordResetToken = $token;
    }

    public function changePassword(string $current, string $new, PasswordHasher $hasher): void
    {
        if ($this->passwordHash === null) {
            throw new \DomainException('User does not have an old password.');
        }
        if (!$hasher->validate($current, $this->passwordHash)) {
            throw new \DomainException('Incorrect current password.');
        }
        $this->passwordHash = $hasher->hash($new);
    }

    public function requestEmailChanging(Token $token,\DateTimeImmutable $date,Email $email)
    {
        if(!$this->isActive()){
            throw new \DomainException('User is not active.');
        }
        if($this->email->isEqualTo($email)){
            throw new \DomainException('Email is already same.');
        }
        if($this->newEmailToken !== null && !$this->newEmailToken->isExpiredTo($date)){
            throw new \DomainException('Changing is already requested.');
        }

        $this->newEmail = $email;
        $this->newEmailToken = $token;
    }

    public function confirmEmailChanging(string $token, \DateTimeImmutable $date): void
    {
        if($this->newEmail === null || $this->newEmailToken === null){
            throw new \DomainException('Changing is not requested.');
        }

        $this->newEmailToken->validate($token, $date);
        $this->email = $this->newEmail;
        $this->newEmail = null;
        $this->newEmailToken = null;
    }

    public function attachNetwork(Network $network): void
    {
        /** @var $existing UserNetwork */
        foreach ($this->networks as $existing){
            if($existing->getNetwork()->isEqual($network)){
                throw new \DomainException('Network is already attached.');
            }
        }
        $this->networks->add(new UserNetwork($this, $network));
    }

    public function resetPassword(string $token, \DateTimeImmutable $date, string $hash): void
    {
        if($this->passwordResetToken === null){
            throw new \DomainException('Resetting is not requested.');
        }
        $this->passwordResetToken->validate($token, $date);
        $this->passwordResetToken = null;
        $this->passwordHash = $hash;
    }

    public function changeRole(Role $role): void
    {
        if($this->role->isEqualTo($role)){
            throw new \DomainException('Role is already same.');
        }
        $this->role = $role;
    }

    public function remove(): void
    {
        if(!$this->isWait()){
            throw new \DomainException('Unable to remove active user.');
        }
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function getSignUpConfirmToken(): ?Token
    {
        return $this->signUpConfirmToken;
    }

    public function getPasswordResetToken(): ?Token
    {
        return $this->passwordResetToken;
    }

    /**
     * @return Network[]
     */
    public function getNetworks(): array
    {
        /** @var Network[] */
        return $this->networks->map(static function (UserNetwork $network) {
            return $network->getNetwork();
        })->toArray();
    }

    public function getNewEmail(): ?Email
    {
        return $this->newEmail;
    }

    public function getNewEmailToken(): ?Token
    {
        return $this->newEmailToken;
    }

    public function isWait(): bool
    {
        return $this->status->isWait();
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    /**
     * обнуляем токены если они пустые
     * метод выполниться после загрузки user из бд
     * @ORM\PostLoad()
     */
    public function checkEmbeds(): void
    {
        if($this->signUpConfirmToken && $this->signUpConfirmToken->isEmpty()){
            $this->signUpConfirmToken = null;
        }
        if($this->passwordResetToken && $this->passwordResetToken->isEmpty()){
            $this->passwordResetToken = null;
        }
        if($this->newEmailToken && $this->newEmailToken->isEmpty()){
            $this->newEmailToken = null;
        }
    }
}

