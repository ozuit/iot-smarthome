import React, { Component } from "react";
import IconMaterialCommunityIcons from 'react-native-vector-icons/MaterialCommunityIcons';

import * as screenNames from "../../navigation/screen_names";

module.exports = [
    {
        roles: ['EMPLOYEE', 'DEVELOPER'],
        name: 'Chấm Công',
        screen: 'EMPLOYEE_CHECKIN',
        icon: <IconMaterialCommunityIcons name="alarm-check" size={40} color="gray" />
    },
    {
        roles: ['EMPLOYEE', 'DEVELOPER'],
        name: 'Xin Phép',
        screen: 'LIST_REQUEST',
        icon: <IconMaterialCommunityIcons name="email-outline" size={40} color="gray" />
    },
    {
        roles: ['ADMIN'],
        name: 'DS Chấm Công',
        screen: 'ADMIN_CHECKIN',
        icon: <IconMaterialCommunityIcons name="alarm-check" size={40} color="gray" />
    },
    {
        roles: ['ADMIN'],
        name: 'Xin Phép',
        screen: 'LIST_REQUEST',
        icon: <IconMaterialCommunityIcons name="email-outline" size={40} color="gray" />
    },
    {
        roles: ['ADMIN', 'DEVELOPER'],
        name: 'Thống Kê',
        screen: 'STATISTIC',
        icon: <IconMaterialCommunityIcons name="table-large" size={40} color="gray" />
    },
    {
        roles: ['ADMIN', 'DEVELOPER'],
        name: 'Xuất Dữ Liệu',
        screen: 'STATISTIC_EXPORT',
        icon: <IconMaterialCommunityIcons name="file-excel" size={40} color="gray" />
    },
    {
        roles: ['DEVELOPER', 'ADMIN'],
        name: 'Người Dùng',
        screen: 'ACCOUNT',
        icon: <IconMaterialCommunityIcons name="account-group" size={40} color="gray" />
    },
    {
        roles: ['EMPLOYEE', 'DEVELOPER', 'ADMIN'],
        name: 'Tài Khoản',
        screen: 'PROFILE',
        icon: <IconMaterialCommunityIcons name="account-card-details-outline" size={40} color="gray" />
    },
    {
        roles: ['EMPLOYEE', 'DEVELOPER', 'ADMIN'],
        name: 'Đăng Xuất',
        screen: 'LOGOUT',
        icon: <IconMaterialCommunityIcons name="logout-variant" size={40} color="gray" />
    },
]