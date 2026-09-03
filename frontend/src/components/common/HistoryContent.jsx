import { useToast } from '@/hooks/toast';
import { actionHistories } from '@/lib/api';
import { formatDate } from '@/utils/functions';
import { Divider, List, ListItem, ListItemText, Typography } from '@mui/material'
import React, { useEffect, useState } from 'react'

const HistoryContent = ({id}) => {
  const [historyList, setHistoryList] = useState()
  const showToast = useToast()

  const fetchHistory = async () => {
    try {
      const response = await actionHistories(id)
      setHistoryList(response?.data)
    } catch (err) {
      showToast("History fetch error!", "error")
    }
  }

  useEffect(() => {
    id && fetchHistory()
  }, [id])

  return (
    <List sx={{ maxHeight: 300, overflow: "auto", border: "1px solid #ddd", borderRadius: 1 }}>
      {historyList?.length > 0 ? historyList?.map((item, index) => (
        <React.Fragment key={item.id}>
          <ListItem>
            <ListItemText
              primary={item?.actions_taken}
              secondary={
                <>
                  <Typography component="span" variant="body2" color="text.secondary">
                    {formatDate(item?.created_at)}
                  </Typography>
                  <br />
                  <Typography component="span" variant="body2" color="text.secondary">
                    By: {item?.team_member?.email}
                  </Typography>
                </>
              }
            />
          </ListItem>
          {index < historyList.length - 1 && <Divider />}
        </React.Fragment>
      )) : <Typography textAlign={"center"}>No history found!</Typography>}
    </List>
  )
}

export default HistoryContent