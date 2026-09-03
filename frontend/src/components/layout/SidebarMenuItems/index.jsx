import { SidebarBottomItems, SidebarTopItems } from '@/data/sideNavItems';
import {
  Divider,
  List,
  ListItem,
  ListItemButton,
  ListItemIcon,
  ListItemText
} from '@mui/material';
import Link from 'next/link';
import React from 'react';

const SidebarMenuItems = () => {
  return (
    <>
      <List>
        {SidebarTopItems.map((item, index) => (
          <ListItem key={index} disablePadding sx={{a: {width: '100%'}}}>
            <Link href={item.path}>
              <ListItemButton>
                <ListItemIcon sx={{minWidth: "40px"}}>
                  {item.icon}
                </ListItemIcon>
                <ListItemText primary={item.title} sx={{span: {fontWeight: 600}}} />
              </ListItemButton>
            </Link>
          </ListItem>
        ))}
      </List>
      <Divider />
      <List>
        {SidebarBottomItems.map((item, index) => (
          <ListItem key={index} disablePadding sx={{a: {width: '100%'}}}>
            <Link href={item.path}>
              <ListItemButton>
                <ListItemIcon sx={{minWidth: "40px"}}>
                  {item.icon}
                </ListItemIcon>
                <ListItemText primary={item.title} sx={{span: {fontWeight: 600}}} />
              </ListItemButton>
            </Link>
          </ListItem>
        ))}
      </List>
    </>
  )
}

export default SidebarMenuItems;