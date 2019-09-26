import React, { Component } from 'react';
import { View, ScrollView, StatusBar } from 'react-native';
import { connect } from 'react-redux';
import Styles from "./Styles";
import MenuItem from "./MenuItem";
import IconMaterialCommunityIcons from 'react-native-vector-icons/MaterialCommunityIcons';
import * as roomApis from '../../services/roomApis';

class HomeContainer extends Component {
    state = {
        menus: []
    }

    componentDidMount() {
        roomApis.all().then((res) => {
            let data = []
            res.data.map((room) => {
                data.push({
                    name: room.name,
                    screen: 'ROOM_DEVICES',
                    icon: <IconMaterialCommunityIcons name={room.icon} size={60} color="gray" />
                })
            })
            this.setState({
                menus: data
            })
        })
    }

    render() {
        const { menus } = this.state

        return (
            <ScrollView>
                <StatusBar backgroundColor="#c60b1e" barStyle="light-content" />
                <View style={Styles.container}>
                    {
                        menus.map((item, index) => (
                            <MenuItem key={index} parent={this.props} menuName={item.name} menuScreen={item.screen}>
                                { item.icon }
                            </MenuItem>
                        ))
                    }
                </View>
            </ScrollView>
        );
    }
}

const mapStateToProps = (state) => ({
    data: {
        userInfo: state.login.userInfo
    }
})

export default connect(mapStateToProps, null)(HomeContainer)