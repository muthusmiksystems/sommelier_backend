<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/protobuf/type.proto

namespace Google\Protobuf;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * A single field of a message type.
 *
 * Generated from protobuf message <code>google.protobuf.Field</code>
 */
class Field extends \Google\Protobuf\Internal\Message
{
    /**
     * The field type.
     *
     * Generated from protobuf field <code>.google.protobuf.Field.Kind kind = 1;</code>
     */
    protected $kind = 0;
    /**
     * The field cardinality.
     *
     * Generated from protobuf field <code>.google.protobuf.Field.Cardinality cardinality = 2;</code>
     */
    protected $cardinality = 0;
    /**
     * The field number.
     *
     * Generated from protobuf field <code>int32 number = 3;</code>
     */
    protected $number = 0;
    /**
     * The field name.
     *
     * Generated from protobuf field <code>string name = 4;</code>
     */
    protected $name = '';
    /**
     * The field type URL, without the scheme, for message or enumeration
     * types. Example: `"type.googleapis.com/google.protobuf.Timestamp"`.
     *
     * Generated from protobuf field <code>string type_url = 6;</code>
     */
    protected $type_url = '';
    /**
     * The index of the field type in `Type.oneofs`, for message or enumeration
     * types. The first type has index 1; zero means the type is not in the list.
     *
     * Generated from protobuf field <code>int32 oneof_index = 7;</code>
     */
    protected $oneof_index = 0;
    /**
     * Whether to use alternative packed wire representation.
     *
     * Generated from protobuf field <code>bool packed = 8;</code>
     */
    protected $packed = false;
    /**
     * The protocol buffer options.
     *
     * Generated from protobuf field <code>repeated .google.protobuf.Option options = 9;</code>
     */
    private $options;
    /**
     * The field JSON name.
     *
     * Generated from protobuf field <code>string json_name = 10;</code>
     */
    protected $json_name = '';
    /**
     * The string value of the default value of this field. Proto2 syntax only.
     *
     * Generated from protobuf field <code>string default_value = 11;</code>
     */
    protected $default_value = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type int $kind
     *           The field type.
     *     @type int $cardinality
     *           The field cardinality.
     *     @type int $number
     *           The field number.
     *     @type string $name
     *           The field name.
     *     @type string $type_url
     *           The field type URL, without the scheme, for message or enumeration
     *           types. Example: `"type.googleapis.com/google.protobuf.Timestamp"`.
     *     @type int $oneof_index
     *           The index of the field type in `Type.oneofs`, for message or enumeration
     *           types. The first type has index 1; zero means the type is not in the list.
     *     @type bool $packed
     *           Whether to use alternative packed wire representation.
     *     @type array<\Google\Protobuf\Option>|\Google\Protobuf\Internal\RepeatedField $options
     *           The protocol buffer options.
     *     @type string $json_name
     *           The field JSON name.
     *     @type string $default_value
     *           The string value of the default value of this field. Proto2 syntax only.
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Google\Protobuf\Type::initOnce();
        parent::__construct($data);
    }

    /**
     * The field type.
     *
     * Generated from protobuf field <code>.google.protobuf.Field.Kind kind = 1;</code>
     * @return int
     */
    public function getKind()
    {
        return $this->kind;
    }

    /**
     * The field type.
     *
     * Generated from protobuf field <code>.google.protobuf.Field.Kind kind = 1;</code>
     * @param int $var
     * @return $this
     */
    public function setKind($var)
    {
        GPBUtil::checkEnum($var, \Google\Protobuf\Field\Kind::class);
        $this->kind = $var;

        return $this;
    }

    /**
     * The field cardinality.
     *
     * Generated from protobuf field <code>.google.protobuf.Field.Cardinality cardinality = 2;</code>
     * @return int
     */
    public function getCardinality()
    {
        return $this->cardinality;
    }

    /**
     * The field cardinality.
     *
     * Generated from protobuf field <code>.google.protobuf.Field.Cardinality cardinality = 2;</code>
     * @param int $var
     * @return $this
     */
    public function setCardinality($var)
    {
        GPBUtil::checkEnum($var, \Google\Protobuf\Field\Cardinality::class);
        $this->cardinality = $var;

        return $this;
    }

    /**
     * The field number.
     *
     * Generated from protobuf field <code>int32 number = 3;</code>
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * The field number.
     *
     * Generated from protobuf field <code>int32 number = 3;</code>
     * @param int $var
     * @return $this
     */
    public function setNumber($var)
    {
        GPBUtil::checkInt32($var);
        $this->number = $var;

        return $this;
    }

    /**
     * The field name.
     *
     * Generated from protobuf field <code>string name = 4;</code>
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * The field name.
     *
     * Generated from protobuf field <code>string name = 4;</code>
     * @param string $var
     * @return $this
     */
    public function setName($var)
    {
        GPBUtil::checkString($var, True);
        $this->name = $var;

        return $this;
    }

    /**
     * The field type URL, without the scheme, for message or enumeration
     * types. Example: `"type.googleapis.com/google.protobuf.Timestamp"`.
     *
     * Generated from protobuf field <code>string type_url = 6;</code>
     * @return string
     */
    public function getTypeUrl()
    {
        return $this->type_url;
    }

    /**
     * The field type URL, without the scheme, for message or enumeration
     * types. Example: `"type.googleapis.com/google.protobuf.Timestamp"`.
     *
     * Generated from protobuf field <code>string type_url = 6;</code>
     * @param string $var
     * @return $this
     */
    public function setTypeUrl($var)
    {
        GPBUtil::checkString($var, True);
        $this->type_url = $var;

        return $this;
    }

    /**
     * The index of the field type in `Type.oneofs`, for message or enumeration
     * types. The first type has index 1; zero means the type is not in the list.
     *
     * Generated from protobuf field <code>int32 oneof_index = 7;</code>
     * @return int
     */
    public function getOneofIndex()
    {
        return $this->oneof_index;
    }

    /**
     * The index of the field type in `Type.oneofs`, for message or enumeration
     * types. The first type has index 1; zero means the type is not in the list.
     *
     * Generated from protobuf field <code>int32 oneof_index = 7;</code>
     * @param int $var
     * @return $this
     */
    public function setOneofIndex($var)
    {
        GPBUtil::checkInt32($var);
        $this->oneof_index = $var;

        return $this;
    }

    /**
     * Whether to use alternative packed wire representation.
     *
     * Generated from protobuf field <code>bool packed = 8;</code>
     * @return bool
     */
    public function getPacked()
    {
        return $this->packed;
    }

    /**
     * Whether to use alternative packed wire representation.
     *
     * Generated from protobuf field <code>bool packed = 8;</code>
     * @param bool $var
     * @return $this
     */
    public function setPacked($var)
    {
        GPBUtil::checkBool($var);
        $this->packed = $var;

        return $this;
    }

    /**
     * The protocol buffer options.
     *
     * Generated from protobuf field <code>repeated .google.protobuf.Option options = 9;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * The protocol buffer options.
     *
     * Generated from protobuf field <code>repeated .google.protobuf.Option options = 9;</code>
     * @param array<\Google\Protobuf\Option>|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setOptions($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Google\Protobuf\Option::class);
        $this->options = $arr;

        return $this;
    }

    /**
     * The field JSON name.
     *
     * Generated from protobuf field <code>string json_name = 10;</code>
     * @return string
     */
    public function getJsonName()
    {
        return $this->json_name;
    }

    /**
     * The field JSON name.
     *
     * Generated from protobuf field <code>string json_name = 10;</code>
     * @param string $var
     * @return $this
     */
    public function setJsonName($var)
    {
        GPBUtil::checkString($var, True);
        $this->json_name = $var;

        return $this;
    }

    /**
     * The string value of the default value of this field. Proto2 syntax only.
     *
     * Generated from protobuf field <code>string default_value = 11;</code>
     * @return string
     */
    public function getDefaultValue()
    {
        return $this->default_value;
    }

    /**
     * The string value of the default value of this field. Proto2 syntax only.
     *
     * Generated from protobuf field <code>string default_value = 11;</code>
     * @param string $var
     * @return $this
     */
    public function setDefaultValue($var)
    {
        GPBUtil::checkString($var, True);
        $this->default_value = $var;

        return $this;
    }

}

