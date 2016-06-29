<?php

namespace Sinergi\Users\User;

use DateTime;
use Sinergi\Users\Utils\Token;

trait UserEntityTrait
{
    protected $id;
    protected $status = UserEntityInterface::STATUS_ACTIVE;
    protected $isAdmin;
    protected $email = null;
    protected $pendingEmail = null;
    protected $deletedEmail = null;
    protected $isEmailConfirmed = false;
    protected $emailConfirmationToken;
    protected $emailConfirmationTokenExpirationDatetime;
    protected $lastEmailTokenGeneratedDatetime;
    protected $password;
    protected $passwordResetToken;
    protected $passwordResetTokenExpirationDatetime;
    protected $lastPasswordResetTokenGeneratedDatetime;
    protected $creationDatetime;
    protected $modificationDatetime;

    public function __construct()
    {
        $this->setCreationDatetime(new DateTime());
        $this->setModificationDatetime(new DateTime());
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): UserEntityInterface
    {
        $this->id = $id;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): UserEntityInterface
    {
        $this->status = $status;
        return $this;
    }

    public function isAdmin(): boolean
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(boolean $isAdmin): UserEntityInterface
    {
        $this->isAdmin = $isAdmin;
        return $this;
    }

    public function isActive(): boolean
    {
        return $this->getStatus() === UserEntityInterface::STATUS_ACTIVE;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): UserEntityInterface
    {
        $this->email = $email;
        return $this;
    }

    public function getPendingEmail(): string
    {
        return $this->pendingEmail;
    }

    public function setPendingEmail(string $pendingEmail): UserEntityInterface
    {
        $this->pendingEmail = $pendingEmail;
        return $this;
    }

    public function isEmailConfirmed(): boolean
    {
        return $this->isEmailConfirmed;
    }

    public function setIsEmailConfirmed(boolean $isEmailConfirmed): UserEntityInterface
    {
        $this->isEmailConfirmed = $isEmailConfirmed;
        return $this;
    }

    public function getEmailConfirmationToken(): string
    {
        return $this->emailConfirmationToken;
    }

    public function setEmailConfirmationToken(string $emailConfirmationToken): UserEntityInterface
    {
        $this->emailConfirmationToken = $emailConfirmationToken;
        return $this;
    }

    public function getEmailConfirmationTokenExpirationDatetime(): DateTime
    {
        return $this->emailConfirmationTokenExpirationDatetime;
    }

    public function setEmailConfirmationTokenExpirationDatetime(
        DateTime $emailConfirmationTokenExpirationDatetime
    ): UserEntityInterface {
        $this->emailConfirmationTokenExpirationDatetime = $emailConfirmationTokenExpirationDatetime;
        return $this;
    }

    public function getLastEmailTokenGeneratedDatetime(): DateTime
    {
        return $this->lastEmailTokenGeneratedDatetime;
    }

    public function setLastEmailTokenGeneratedDatetime(
        DateTime $lastEmailTokenGeneratedDatetime
    ): UserEntityInterface {
        $this->lastEmailTokenGeneratedDatetime = $lastEmailTokenGeneratedDatetime;
        return $this;
    }

    public function canGenerateNewEmailConfirmationToken(): boolean
    {
        $lastGenerated = $this->getLastEmailTokenGeneratedDatetime();
        return (
            empty($lastGenerated) ||
            (new DateTime())->getTimestamp() - $lastGenerated->getTimestamp() > UserEntityInterface::EMAIL_COOLDOWN
        );
    }

    public function generateEmailConfirmationToken(): UserEntityInterface
    {
        if ($this->canGenerateNewEmailConfirmationToken()) {
            $this->setEmailConfirmationToken(Token::generate(40));
            $this->setLastEmailTokenGeneratedDatetime(new DateTime());
        }
        return $this;
    }

    public function getDeletedEmail(): string
    {
        return $this->deletedEmail;
    }

    public function setDeletedEmail(string $deletedEmail): UserEntityInterface
    {
        $this->deletedEmail = $deletedEmail;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): UserEntityInterface
    {
        $this->password = $password;
        $this->hashPassword();
        return $this;
    }

    public function getPasswordResetToken(): string
    {
        return $this->passwordResetToken;
    }

    public function setPasswordResetToken(string $passwordResetToken): UserEntityInterface
    {
        $this->passwordResetToken = $passwordResetToken;
        return $this;
    }

    public function getPasswordResetTokenExpirationDatetime(): DateTime
    {
        return $this->passwordResetTokenExpirationDatetime;
    }

    public function setPasswordResetTokenExpirationDatetime(
        DateTime $passwordResetTokenExpirationDatetime
    ): UserEntityInterface {
        $this->passwordResetTokenExpirationDatetime = $passwordResetTokenExpirationDatetime;
        return $this;
    }

    public function getLastPasswordResetTokenGeneratedDatetime(): DateTime
    {
        return $this->lastPasswordResetTokenGeneratedDatetime;
    }

    public function setLastPasswordResetTokenGeneratedDatetime(
        DateTime $lastPasswordResetTokenGeneratedDatetime
    ): UserEntityInterface {
        $this->lastPasswordResetTokenGeneratedDatetime = $lastPasswordResetTokenGeneratedDatetime;
        return $this;
    }

    public function canGenerateNewResetPasswordToken(): boolean
    {
        $lastGenerated = $this->getLastPasswordResetTokenGeneratedDatetime();
        return (
            empty($lastGenerated) ||
            (new DateTime())->getTimestamp() - $lastGenerated->getTimestamp() > UserEntityInterface::EMAIL_COOLDOWN
        );
    }

    public function generatePasswordResetToken(): UserEntityInterface
    {
        if ($this->canGenerateNewResetPasswordToken()) {
            $this->setPasswordResetToken(Token::generate(40));
            $this->setLastPasswordResetTokenGeneratedDatetime(new DateTime());
        }
        return $this;
    }

    protected function hashPassword(): UserEntityInterface
    {
        $this->password = password_hash(
            (string) $this->password,
            PASSWORD_DEFAULT
        );
        return $this;
    }

    public function testPassword(string $password): boolean
    {
        return password_verify($password, $this->password);
    }

    public function getCreationDatetime(): DateTime
    {
        return $this->creationDatetime;
    }

    public function setCreationDatetime(DateTime $creationDatetime): UserEntityInterface
    {
        $this->creationDatetime = $creationDatetime;
        return $this;
    }

    public function getModificationDatetime(): DateTime
    {
        return $this->modificationDatetime;
    }

    public function setModificationDatetime(DateTime $modificationDatetime): UserEntityInterface
    {
        $this->modificationDatetime = $modificationDatetime;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'status' => $this->getStatus(),
            'email' => $this->getEmail(),
            'pendingEmail' => $this->getPendingEmail(),
            'deletedEmail' => $this->getDeletedEmail(),
            'isEmailConfirmed' => $this->isEmailConfirmed(),
            'emailConfirmationToken' => $this->getEmailConfirmationToken(),
            'emailConfirmationTokenExpirationDatetime' => $this->getEmailConfirmationTokenExpirationDatetime()
                ->format('Y-m-d H:i:s'),
            'lastEmailTokenGeneratedDatetime' => $this->getEmailConfirmationTokenExpirationDatetime()
                ->format('Y-m-d H:i:s'),
            'password' => $this->getPassword(),
            'passwordResetToken' => $this->getPasswordResetToken(),
            'passwordResetTokenExpirationDatetime' => $this->getPasswordResetTokenExpirationDatetime()
                ->format('Y-m-d H:i:s'),
            'lastPasswordResetTokenGeneratedDatetime' => $this->getLastPasswordResetTokenGeneratedDatetime()
                ->format('Y-m-d H:i:s'),
            'isAdmin' => $this->isAdmin(),
            'creationDatetime' => $this->getCreationDatetime()->format('Y-m-d H:i:s'),
            'modificationDatetime' => $this->getModificationDatetime()->format('Y-m-d H:i:s'),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}