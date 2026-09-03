import React from 'react'
import { TextField } from "@mui/material"

const TextFieldCommon = ({
  id,
  label,
  type,
  name,
  disabledStatus,
  errorStatus,
  requiredStatus,
  value,
  onchangeInputValue,
  placeholder=''
}) => {
  return (
    <TextField
      sx={{width: "100%"}}
      size="small"
      id={id}
      label={label}
      variant="standard"
      disabled={disabledStatus ?? false}
      error={errorStatus ?? false}
      name={name}
      required={requiredStatus ?? true}
      type={type}
      value={value}
      onChange={onchangeInputValue}
      placeholder={placeholder}
    />
  )
}

export default TextFieldCommon