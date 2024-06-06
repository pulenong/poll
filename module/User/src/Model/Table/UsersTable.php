<?php

declare(strict_types=1);

namespace User\Model\Table;

use Laminas\Crypt\Password\BcryptSha;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\Sql\TableIdentifier;
use Laminas\Db\TableGateway\AbstractTableGateway;
use Laminas\Filter\DateSelect;
use Laminas\Filter\File\RenameUpload;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\I18n\Filter\Alnum;
use Laminas\I18n\Validator\Alnum as ValidatorAlnum;
use Laminas\I18n\Validator\IsInt;
use Laminas\InputFilter\Factory;
use Laminas\InputFilter\InputFilter;
use Laminas\Paginator\Adapter\LaminasDb\DbSelect;
use Laminas\Paginator\Paginator;
use Laminas\Validator\Date;
use Laminas\Validator\Db\NoRecordExists;
use Laminas\Validator\Db\RecordExists;
use Laminas\Validator\EmailAddress;
use Laminas\Validator\File\Extension;
use Laminas\Validator\File\IsImage;
use Laminas\Validator\File\Size;
use Laminas\Validator\Identical;
use Laminas\Validator\InArray;
use Laminas\Validator\NotEmpty;
use Laminas\Validator\StringLength;
use User\Model\Entity\UserEntity;

class UsersTable extends AbstractTableGateway
{
    /** @var string|array|TableIdentifier */
    protected $table = 'users';

    /**
     * @param Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    /**
     * Delete user account belonging to provided $userId
     *
     * @param int $userId
     * @return ResultInterface
     */
    public function deleteAccount(int $userId): ResultInterface
    {
        $query = $this->sql->delete()->where(['user_id' => $userId]);
        $statement = $this->sql->prepareStatementForSqlObject($query);
        return $statement->execute();
    }

    /**
     * Find all active accounts
     *
     * @param bool $paginate
     * @return HydratingResultSet|Paginator
     */
    public function findAll(bool $paginate = false): HydratingResultSet|Paginator
    {
        $query = $this->sql->select()
                   ->join('user_roles', 'user_roles.role_id='.$this->table.'.role_id', 'role')
                   ->order('created ASC');
        
        $resultSet = new HydratingResultSet(new ClassMethodsHydrator(), new UserEntity());

        if ($paginate) {
            $paginatorAdapter = new DbSelect($query, $this->adapter, $resultSet);
            return new Paginator($paginatorAdapter);
        }

        $statement = $this->sql->prepareStatementForSqlObject($query);
        $result = $statement->execute();

        $resultSet->initialize($result);
        return $resultSet;
    }

    /**
     * Search data by $email provided
     *
     * @param string $email
     * @return UserEntity|null
     */
    public function findByEmail(string $email): ?UserEntity
    {
        $query = $this->sql->select()->join(
            'user_roles', 'user_roles.role_id='.$this->table.'.role_id', 'role'
        )->where(['email' => $email]);

        $statement = $this->sql->prepareStatementForSqlObject($query);
        $result = $statement->execute()->current();

        if (!$result) { return null; }

        $entity = new UserEntity();
        (new ClassMethodsHydrator())->hydrate($result, $entity);

        return $entity;
    }

    /**
     * Search data matching $userId provided
     *
     * @param int $userId
     * @return UserEntity|null
     */
    public function findById(int $userId): ?UserEntity
    {
        $query = $this->sql->select()->join(
            'user_roles', 'user_roles.role_id='. $this->table . '.role_id', 'role'
        )->where(['user_id' => $userId]);

        $statement = $this->sql->prepareStatementForSqlObject($query);
        $result = $statement->execute()->current();

        if (!$result) { return null; }

        $entity = new UserEntity();
        (new ClassMethodsHydrator())->hydrate($result, $entity);

        return $entity;
    }

    /**
     * Search data matching $username provided
     *
     * @param string $username
     * @return UserEntity|null
     */
    public function findByUsername(string $username): ?UserEntity
    {
        $query = $this->sql->select()->join(
            'user_roles', 'user_roles.role_id='.$this->table.'.role_id', 'role'
        )->where(['username' => $username]);

        $statement = $this->sql->prepareStatementForSqlObject($query);
        $result = $statement->execute()->current();

        if (!$result) { return null; }

        $entity = new UserEntity();
        (new ClassMethodsHydrator())->hydrate($result, $entity);

        return $entity;
    }

    /**
     * Filter and validate form data from the AvatarForm
     *
     * @return InputFilter
     */
    public function getAvatarFormSanitizer(): InputFilter
    {
        $inputFilter = new InputFilter();
        $factory = new Factory();

        $inputFilter->add(
            $factory->createInput([
                'name' => 'picture',
                'required' => true,
                'validators' => [
                    ['name' => NotEmpty::class],
                    ['name' => IsImage::class],
                    [
                        'name' => Extension::class,
                        'options' => [
                            'extension' => 'gif, jpeg, jpg, png',
                        ],
                    ],
                    [
                        'name' => Size::class,
                        'options' => [
                            'min' => '3kB',
                            'max' => '10MB',
                        ],
                    ],
                ],
                'filters' => [
                    [
                        'name' => RenameUpload::class,
                        'options' => [
                            'target' => 'public'.DIRECTORY_SEPARATOR.'temporary'.DIRECTORY_SEPARATOR.'user',
                            'use_upload_name' => false,
                            'use_upload_extension' => true,
                            'overwrite'=> false,
                            'randomize' => true,
                        ],
                    ],
                ],
            ])
        );

        return $inputFilter;
    }

    /**
     * Filter and validate form data from the EmailForm
     *
     * @return InputFilter
     */
    public function getEmailFormSanitizer(): InputFilter
    {
        $inputFilter = new InputFilter();
        $factory = new Factory();

        $inputFilter->add(
            $factory->createInput([
                'name' => 'currentEmail',
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
                'validators' => [
                    ['name' => NotEmpty::class],
                    ['name' => EmailAddress::class],
                    [
                        'name' => RecordExists::class,
                        'options' => [
                            'table' => $this->table,
                            'field' => 'email',
                            'adapter' => $this->adapter,
                        ],
                    ],
                ],
            ])
        );

        $inputFilter->add(
            $factory->createInput([
                'name' => 'newEmail',
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
                'validators' => [
                    ['name' => NotEmpty::class],
                    ['name' => EmailAddress::class],
                    [
                        'name' => NoRecordExists::class,
                        'options' => [
                            'table' => $this->table,
                            'field' => 'email',
                            'adapter' => $this->adapter,
                        ],
                    ],
                ],
            ])
        );

        return $inputFilter;
    }

    /**
     * Filter and validate form data from the ForgotForm
     *
     * @return InputFilter
     */
    public function getForgotFormSanitizer(): InputFilter
    {
        $inputFilter = new InputFilter();
        $factory = new Factory();

        $inputFilter->add(
            $factory->createInput([
                'name' => 'email',
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
                'validators' => [
                    ['name' => NotEmpty::class],
                    ['name' => EmailAddress::class],
                    [
                        'name' => RecordExists::class,
                        'options' => [
                            'table' => $this->table,
                            'field' => 'email',
                            'adapter' => $this->adapter,
                        ],
                    ],
                ],
            ])
        );

        return $inputFilter;
    }

    /**
     * Filter and validate form data from the LoginForm
     *
     * @return InputFilter
     */
    public function getLoginFormSanitizer(): InputFilter
    {
        $inputFilter = new InputFilter();
        $factory = new Factory();

        $inputFilter->add(
            $factory->createInput([
                'name' => 'email',
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
                'validators' => [
                    ['name' => NotEmpty::class],
                    [
                        'name' => EmailAddress::class,
                        'options' => [
                            'messages' => [
                                EmailAddress::INVALID_FORMAT => 'Invalid email address format!',
                            ],
                        ],
                    ],
                    [
                        'name' => RecordExists::class,
                        'options' => [
                            'table' => $this->table,
                            'field' => 'email',
                            'adapter' => $this->adapter,
                        ]
                    ],
                ],
            ])
        );

        // sanitize password input field
        $inputFilter->add(
            $factory->createInput([
                'name' => 'password',
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
                'validators' => [
                    ['name' => NotEmpty::class],
                    [
                        'name' => StringLength::class,
                        'options' => [
                            'min' => 8,
                            'max' => 25,
                            'messages' => [
                                StringLength::TOO_SHORT => 'Password must have at least 8 characters',
                                StringLength::TOO_LONG => 'Password must have at most 25 characters',
                            ]
                        ]
                    ],
                ],
            ])
        );

        // sanitize the check input field
        $inputFilter->add(
            $factory->createInput([
                'name' => 'recall',
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                    ['name' => ToInt::class],
                ],
                'validators' => [
                    ['name' => NotEmpty::class],
                    ['name' => IsInt::class],
                    [
                        'name' => InArray::class,
                        'options' => [
                            'haystack' => ['0', '1'],
                            'strict' => InArray::COMPARE_NOT_STRICT,
                        ],
                    ],
                ],
            ])
        );

        return $inputFilter;
    }

    /**
     * Filter and validate form data from the PasswordForm
     *
     * @return InputFilter
     */
    public function getPasswordFormSanitizer(): InputFilter
    {
        $inputFilter = new InputFilter();
        $factory = new Factory();

        $inputFilter->add(
            $factory->createInput([
                'name' => 'currentPassword',
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
                'validators' => [
                    ['name' => NotEmpty::class],
                    [
                        'name' => StringLength::class,
                        'options' => [
                            'min' => 8,
                            'max' => 25,
                            'messages' => [
                                StringLength::TOO_SHORT => 'Password must have at least 8 characters, a lowercase, an uppercase character and a digit',
                                StringLength::TOO_LONG => 'Password must have at most 25 characters, a lowercase, an uppercase character and a digit',
                            ],
                        ],
                    ],
                ],
            ])
        );

        $inputFilter->add(
            $factory->createInput([
                'name' => 'newPassword',
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
                'validators' => [
                    ['name' => NotEmpty::class],
                    [
                        'name' => StringLength::class,
                        'options' => [
                            'min' => 8,
                            'max' => 25,
                            'messages' => [
                                StringLength::TOO_SHORT => 'Password must have at least 8 characters, a digit, a lowercase and an uppercase character',
                                StringLength::TOO_LONG => 'Password must have at most 25 characters, a digit, a lowercase and an uppercase character',
                            ],
                        ],
                    ],
                ],
            ])
        );

        $inputFilter->add(
            $factory->createInput([
                'name' => 'confirmNewPassword',
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
                'validators' => [
                    ['name' => NotEmpty::class],
                    [
                        'name' => Identical::class,
                        'options' => [
                            'token' => 'newPassword',
                            'messages' => [
                                Identical::NOT_SAME => 'New Passwords do not match! Makes sure that they match.',
                            ],
                        ],
                    ],
                ],
            ])
        );

        return $inputFilter;
    }

    /**
     * Filter and validate form data from the RegisterForm
     *
     * @return InputFilter
     */
    public function getRegisterFormSanitizer(): InputFilter
    {
        $inputFilter = new InputFilter();
        $factory = new Factory();

        // filter and validate username input field
        $inputFilter->add(
            $factory->createInput([
                'name' => 'username',
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                    ['name' => Alnum::class],
                ],
                'validators' => [
                    ['name' => NotEmpty::class],
                    [
                        'name' => StringLength::class,
                        'options' => [
                            'min' => 2,
                            'max' => 25,
                            'messages' => [
                                StringLength::TOO_SHORT => 'Username must have at least 2 characters',
                                StringLength::TOO_LONG => 'Username must have at most 25 characters',
                            ],
                        ],
                    ],
                    [
                        'name' => ValidatorAlnum::class,
                        'options' => [
                            'messages' => [
                                ValidatorAlnum::NOT_ALNUM => 'Username must consist of alphanumeric characters only',
                            ],
                        ],
                    ],
                    [
                        'name' => NoRecordExists::class,
                        'options' => [
                            'table' => $this->table,
                            'field' => 'username',
                            'adapter' => $this->adapter,
                        ]
                    ]
                ]
            ])
        );

        // filter and validate the email address input field
        $inputFilter->add(
            $factory->createInput([
                'name' => 'email',
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
                'validators' => [
                    ['name' => NotEmpty::class],
                    [
                        'name' => EmailAddress::class,
                        'options' => [
                            'messages' => [
                                EmailAddress::INVALID_FORMAT => 'Invalid email address format!',
                            ],
                        ],
                    ],
                    [
                        'name' => NoRecordExists::class,
                        'options' => [
                            'table' => $this->table,
                            'field' => 'email',
                            'adapter' => $this->adapter,
                        ]
                    ],
                ]
            ])
        );

        // filter and validate the birthdate select field
        $inputFilter->add(
            $factory->createInput([
                'name' => 'birthdate',
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                    ['name' => DateSelect::class],
                ],
                'validators' => [
                    ['name' => NotEmpty::class],
                    [
                        'name' => Date::class,
                        'options' => [
                            'format' => 'Y-m-d',
                        ],
                    ],
                ]
            ])
        );

        // filter and validate the password input field
        $inputFilter->add(
            $factory->createInput([
                'name' => 'password',
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
                'validators' => [
                    ['name' => NotEmpty::class],
                    [
                        'name' => StringLength::class,
                        'options' => [
                            'min' => 8,
                            'max' => 25,
                            'messages' => [
                                StringLength::TOO_SHORT => 'Password must have at least 8 characters',
                                StringLength::TOO_LONG => 'Password must have at most 25 characters'
                            ],
                        ],
                    ],
                ]
            ])
        );

        // filter and validate the confirmPassword input field
        $inputFilter->add(
            $factory->createInput([
                'name' => 'confirmPassword',
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
                'validators' => [
                    ['name' => NotEmpty::class],
                    [
                        'name' => Identical::class,
                        'options' => [
                            'token' => 'password',
                            'messages' => [
                                Identical::NOT_SAME => 'Passwords do not match! Make sure that they match.',
                            ],
                        ],
                    ],
                ]
            ])
        );

        return $inputFilter;
    }

    /**
     * Filter and validate form data from the ResetForm
     *
     * @return InputFilter
     */
    public function getResetFormSanitizer(): InputFilter
    {
        $inputFilter = new InputFilter();
        $factory = new Factory();

        $inputFilter->add(
            $factory->createInput([
                'name' => 'newPassword',
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
                'validators' => [
                    ['name' => NotEmpty::class],
                    [
                        'name' => StringLength::class,
                        'options' => [
                            'min' => 8,
                            'max' => 25,
                            'messages' => [
                                StringLength::TOO_SHORT => 'New Password must have at least 8 characters',
                                StringLength::TOO_LONG => 'New Password must have at most 25 characters',
                            ],
                        ],
                    ],
                ],
            ])
        );

        $inputFilter->add(
            $factory->createInput([
                'name' => 'confirmNewPassword',
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
                'validators' => [
                    ['name' => NotEmpty::class],
                    [
                        'name' => Identical::class,
                        'options' => [
                            'token' => 'newPassword',
                            'messages' => [
                                Identical::NOT_SAME => 'New Passwords do not match! Makes sure they match.',
                            ],
                        ],
                    ],
                ],
            ])
        );

        return $inputFilter;
    }

    /**
     * Filter and validate form data from the UsernameForm
     *
     * @return InputFilter
     */
    public function getUsernameFormSanitizer(): InputFilter
    {
        $inputFilter = new InputFilter();
        $factory = new Factory();

        $inputFilter->add($factory->createInput([
            'name' => 'currentUsername',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
                ['name' => Alnum::class]
            ],
            'validators' => [
                ['name' => NotEmpty::class],
                [
                    'name' => StringLength::class,
                    'options' => [
                        'min' => 2,
                        'max' => 25,
                        'messages' => [
                            StringLength::TOO_SHORT => 'Current Username must have at least 2 characters',
                            StringLength::TOO_LONG => 'Current Username must have at most 25 characters',
                        ],
                    ],
                ],
                [
                    'name' => ValidatorAlnum::class,
                    'options' => [
                        'messages' => [
                            ValidatorAlnum::NOT_ALNUM => 'Username must consist of alphanumeric characters only',
                        ],
                    ],
                ],
                [
                    'name' => RecordExists::class,
                    'options' => [
                        'table' => $this->table,
                        'field' => 'username',
                        'adapter' => $this->adapter,
                    ],
                ],
            ]
        ]));

        $inputFilter->add($factory->createInput([
            'name' => 'newUsername',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
                ['name' => Alnum::class]
            ],
            'validators' => [
                ['name' => NotEmpty::class],
                [
                    'name' => StringLength::class,
                    'options' => [
                        'min' => 2,
                        'max' => 25,
                        'messages' => [
                            StringLength::TOO_SHORT => 'New username must have at least 2 characters',
                            StringLength::TOO_LONG => 'New username must have at most 25 characters',
                        ],
                    ],
                ],
                [
                    'name' => ValidatorAlnum::class,
                    'options' => [
                        'messages' => [
                            ValidatorAlnum::NOT_ALNUM => 'Username must consist of alphanumeric characters only',
                        ],
                    ],
                ],
                [
                    'name' => NoRecordExists::class,
                    'options' => [
                        'table' => $this->table,
                        'field' => 'username',
                        'adapter' => $this->adapter,
                    ],
                ],
            ]
        ]));

        return $inputFilter;
    }

    /**
     * Save account data
     *
     * @param array $data
     * @return ResultInterface
     */
    public function insertAccount(array $data): ResultInterface
    {
        $values = [
            'username' => ucfirst(mb_strtolower($data['username'])),
            'email' => mb_strtolower($data['email']),
            'password' => (new BcryptSha())->create($data['password']),
            'birthdate' => $data['birthdate'],
            'role_id' => $this->assignRoleId(),
            'created' => date('Y-m-d H:i:s'),
            'updated' => date('Y-m-d H:i:s')
        ];

        $query = $this->sql->insert()->values($values);
        $statement = $this->sql->prepareStatementForSqlObject($query);

        return $statement->execute();
    }

    /**
     * Update $email address belong to $userId provided
     *
     * @param string $email
     * @param int $userId
     * @return ResultInterface
     */
    public function updateEmail(string $email, int $userId): ResultInterface
    {
        $values = [
            'email' => mb_strtolower($email),
            'updated' => date('Y-m-d H:i:s')
        ];

        $query = $this->sql->update()->set($values)->where(['user_id' => $userId]);
        $statement = $this->sql->prepareStatementForSqlObject($query);

        return $statement->execute();
    }

    /**
     * Update $password belonging to $userId provided
     *
     * @param string $password
     * @param int $userId
     * @return ResultInterface
     */
    public function updatePassword(string $password, int $userId): ResultInterface
    {
        $values = [
            'password' => (new BcryptSha())->create($password),
            'updated' => date('Y-m-d H:i:s')
        ];

        $query = $this->sql->update()->set($values)->where(['user_id' => $userId]);
        $statement = $this->sql->prepareStatementForSqlObject($query);

        return $statement->execute();
    }

    /**
     * Update $picture belonging to $userId provided
     *
     * @param string $picture
     * @param int $userId
     * @return ResultInterface
     */
    public function updatePicture(string $picture, int $userId): ResultInterface
    {
        $values = [
            'picture' => $picture,
            'updated' => date('Y-m-d H:i:s')
        ];

        $query = $this->sql->update()->set($values)->where(['user_id' => $userId]);
        $statement = $this->sql->prepareStatementForSqlObject($query);

        return $statement->execute();
    }

    /**
     * Update $username belonging to $userId provided
     *
     * @param string $username
     * @param int $userId
     * @return ResultInterface
     */
    public function updateUsername(string $username, int $userId): ResultInterface
    {
        $values = [
            'username' => ucfirst(mb_strtolower($username)),
            'updated' => date('Y-m-d H:i:s')
        ];

        $query = $this->sql->update()->set($values)->where(['user_id' => $userId]);
        $statement = $this->sql->prepareStatementForSqlObject($query);

        return $statement->execute();
    }

    /**
     * Assign users roles. First check if anyone already has role_id == 1.
     *
     * @return int
     */
    private function assignRoleId(): int
    {
        $query = $this->sql->select()->where(['role_id' => 1]);
        $statement = $this->sql->prepareStatementForSqlObject($query);
        $result = $statement->execute()->current();

        return (!$result) ? 1 : 2;
    }
}
