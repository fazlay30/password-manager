import { useToast } from '@/hooks/toast';
import { getNotifications } from '@/lib/api';
import { formatDate } from '@/utils/functions';
import { Divider, List, ListItem, ListItemText, Typography } from '@mui/material'
import React, { useEffect, useState } from 'react'

const NotificationContent = () => {
  const [notifyList, setNotifyList] = useState()
  const showToast = useToast()

  const fetchHistory = async () => {
    try {
      const response = await getNotifications()
      setNotifyList(response)
    } catch (err) {
      showToast("Notification fetch error!", "error")
    }
  }

  useEffect(() => {
    fetchHistory()
  }, [])

  return (
    <List sx={{ maxHeight: 300, overflow: "auto", border: "1px solid #ddd", borderRadius: 1 }}>
      {notifyList?.length > 0 ? notifyList?.map((item, index) => (
        <React.Fragment key={index}>
          <ListItem>
            <ListItemText
              primary={item?.data?.data}
              secondary={formatDate(item?.created_at)}
            />
          </ListItem>
          {index < notifyList.length - 1 && <Divider />}
        </React.Fragment>
      )) : <Typography textAlign={"center"}>No notification found!</Typography>}
    </List>
  )
}

export default NotificationContent