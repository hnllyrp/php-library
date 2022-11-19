<?php

namespace Hnllyrp\PhpSupport;


class Arr
{

    /**
     * 一维数组指定任何位置增加一个值
     * @param array $array
     * @param int $offset
     * @param int $length
     * @param array $value
     * @return array|mixed
     */
    public static function array_push_one(array $array = [], int $offset = 0, int $length = 0, array $value = [])
    {
        array_splice($array, $offset, $length, $value);

        return $array;
    }

    /**
     * 去掉二维数组中的重复项
     * @param array $array2D 数组
     * @param array $keyArray 还原时字段对应的key
     * @return array  去掉了重复项的数组
     *
     *  例如  $new_arr = array_unique_multi($old_arr, ['keyword']); // 去重
     *        $new_arr = array_values($new_arr); // 数组重新索引
     */
    public static function array_unique_multi($array2D = [], $keyArray = [])
    {
        $temp = [];
        foreach ($array2D as $v) {
            $v = join(",", $v);  //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
            $temp[] = $v;
        }
        $temp = array_unique($temp);    //去掉重复的字符串,也就是重复的一维数组

        $new_arr = [];
        foreach ($temp as $k => $v) {
            // $temp[$k] = explode(",",$v);   //再将拆开的数组重新组装
            $new_arr[$k] = array_combine($keyArray, explode(",", trim($v)));
        }
        return $new_arr;
    }

    /**
     * 将数组里的null值转为空
     * @param array $data
     * @return array|mixed
     */
    public static function array_filter_null(array $data = [])
    {
        array_walk_recursive($data, function (&$val, $key) {
            $val = ($val === null) ? '' : $val;
        });

        return $data;
    }

    /**
     * 清空多维数组 空值
     * @param array $arr
     * @return array|mixed
     */
    public static function array_no_empty($arr = [])
    {
        if (is_array($arr)) {
            foreach ($arr as $k => $v) {
                if (empty($v)) unset($arr[$k]);
                elseif (is_array($v)) {
                    $arr[$k] = self::array_no_empty($v);
                }
            }
        }
        return $arr;
    }

    /**
     * 将数组里的null值过滤掉
     * @param array $arr
     * @return array|mixed
     */
    public static function array_null_filter(array $arr = [])
    {
        foreach ($arr as $key => &$val) {
            if (is_array($val)) {
                $val = self::array_null_filter($val);
            } else {
                if ($val === null) {
                    unset($arr[$key]);
                }
            }
        }
        return $arr;
    }

    /**
     * 数组 转 对象
     *
     * @param array $arr 数组
     * @return object
     */
    public static function array_to_object($arr)
    {
        if (gettype($arr) != 'array') {
            return null;
        }
        foreach ($arr as $k => $v) {
            if (gettype($v) == 'array' || getType($v) == 'object') {
                $arr[$k] = (object)self::array_to_object($v);
            }
        }

        return (object)$arr;
    }


    /**
     * 二维数组查找值
     *
     * @param $key
     * @param $value
     * @param $array
     *
     * @return mixed
     */
    public function array_search_filter($key, $value, $array)
    {
        $array = array_filter($array, function ($item) use ($key, $value) {
            return $item[$key] === $value;
        });

        return reset($array);
    }

    /**
     * 数组查找 index.
     *
     * @param $node
     * @param $array
     *
     * @return bool|mixed
     */
    public function array_search_index($node, $array)
    {
        $flip_array = array_flip($array);

        return isset($flip_array[$node]) ? $flip_array[$node] : false;
    }

    /**
     * 递归显示所有分类
     * @param int $cat_id
     * @param int $level
     * @return array
     */
    public static function category_list($cat_id = 0, $level = 0)
    {
        $res = [
            ['cat_id' => 1, 'parent_id' => 0, 'cat_name' => '分类1'],
            ['cat_id' => 2, 'parent_id' => 0, 'cat_name' => '分类2'],
            ['cat_id' => 3, 'parent_id' => 0, 'cat_name' => '分类3'],
            ['cat_id' => 4, 'parent_id' => 1, 'cat_name' => '分类4']
        ];

        //声明静态数组,避免递归调用时,多次声明导致数组覆盖
        static $list = [];
        foreach ($res as $key => $value) {
            //第一次遍历,找到父节点为根节点的节点 也就是parent_id=0的节点
            if ($value['parent_id'] == $cat_id) {
                //父节点为根节点的节点,级别为0，也就是第一级
                $value['level'] = $level;
                //把数组放到list中
                $list[] = $value;
                //把这个节点从数组中移除,减少后续递归消耗
                unset($res[$key]);
                //开始递归,查找父ID为该节点ID的节点,级别则为原级别+1
                self::category_list($value['cat_id'], $level + 1);

            }
        }

        return $list;
    }

    /**
     * 对两个二维数组取差集 - 去除$arr1 中 存在和$arr2相同的部分之后的内容
     * @param array $arr1
     * @param array $arr2
     * @return array
     */
    public static function diffArrayByFilter(array $arr1 = [], array $arr2 = [])
    {
        try {
            return array_filter($arr1, function ($v) use ($arr2) {
                return !in_array($v, $arr2);
            });
        } catch (\Exception $exception) {
            return $arr1;
        }
    }

    /**
     * 根据唯一字段对两个二维数组取差集
     *  - 去除$arr1 中 存在和$arr2相同的部分之后的内容 - 返回差集数组
     * @param array $arr1
     * @param array $arr2
     * @param string $pk
     * @return array
     */
    public static function diffArrayByPk(array $arr1 = [], array $arr2 = [], string $pk = 'pid')
    {
        try {
            $res = [];
            foreach ($arr2 as $item) {
                $tmpArr[$item[$pk]] = $item;
            }
            foreach ($arr1 as $v) {
                if (!isset($tmpArr[$v[$pk]])) {
                    $res[] = $v;
                }
            }
            return $res;
        } catch (\Exception $exception) {
            return $arr1;
        }
    }

    /*
     * 判断多维数组是否存在某个值
     * $arr = [
           ['a', 'b'],
           ['c', 'd']
        ];
        deep_in_array('a', $arr); // 此时返回 true 值
     *
     */
    public static function deep_in_array($value = '', $array = [])
    {
        foreach ($array as $item) {
            if (!is_array($item)) {
                if ($item == $value) {
                    return true;
                } else {
                    continue;
                }
            }
            if (in_array($value, $item)) {
                return true;
            } else if (self::deep_in_array($value, $item)) {
                return true;
            }
        }
        return false;
    }


    /**
     * 数组根据某字段进行分组
     * @param array $dataArr 需要分组的数据
     * @param string $keyStr 分组依据
     * @return array
     */
    function data_group($dataArr = [], $keyStr = '')
    {
        $newArr = [];
        foreach ($dataArr as $k => $val) {
            $newArr[$val[$keyStr]][] = $val;
        }
        return $newArr;
    }

    /**
     * 合并多维数组 如果有重复键值的则合并
     * merge multi-dimentional array
     * @param array $arr1
     * @param array $arr2
     * @return array|mixed
     */
    public static function merge_multidimensional_array(array $arr1 = [], array $arr2 = [])
    {
        foreach ($arr2 as $key => $value) {
            if (array_key_exists($key, $arr1) && is_array($value)) {
                $arr1[$key] = self::merge_multidimensional_array($arr1[$key], $arr2[$key]);
            } else {
                $arr1[$key] = $value;
            }
        }

        return $arr1;
    }

    /**
     * 对象 转 数组
     *
     * @param object $obj 对象
     * @return array
     */
    public static function object_to_array($obj)
    {
        $obj = (array)$obj;
        foreach ($obj as $k => $v) {
            if (gettype($v) == 'resource') {
                return [];
            }
            if (gettype($v) == 'object' || gettype($v) == 'array') {
                $obj[$k] = (array)self::object_to_array($v);
            }
        }

        return $obj;
    }

    /**
     * 数组分页函数 核心函数 array_slice
     * 用此函数之前要先将数据库里面的所有数据按一定的顺序查询出来存入数组中
     * $page_size  每页多少条数据
     * $page  当前第几页
     * $array  查询出来的所有数组
     * order 0 - 不变   1- 反序
     */
    function page_array($page_size = 1, $page = 1, $array = [], $order = 0, $filter_arr = [])
    {
        $arr = [];
        if ($array) {
            global $countpage; //定全局变量

            $start = ($page - 1) * $page_size; //计算每次分页的开始位置

            if ($order == 1) {
                $array = array_reverse($array);
            }

            $totals = count($array);
            $countpage = ceil($totals / $page_size); //计算总页面数
            $pagedata = array_slice($array, $start, $page_size);

            $filter = [
                'page' => $page,
                'page_size' => $page_size,
                'record_count' => $totals,
                'page_count' => $countpage
            ];

            if ($filter_arr) {
                $filter = array_merge($filter, $filter_arr);
            }

            $arr = ['list' => $pagedata, 'filter' => $filter, 'page_count' => $countpage, 'record_count' => $totals];
        }

        return $arr; //返回查询数据
    }


}
